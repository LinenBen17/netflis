<?php

namespace App\Services;

use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Http;

class netflisApiService
{
    public function recommend($selectedMoviesID)
    {
        // Enviar solicitud a la API de Python para recomendaciones
        $response = Http::withoutVerifying()->post('http://localhost:5000/recomendar', [
            'movie_ids' => $selectedMoviesID,
            'num_recomendaciones' => 10,
        ]);

        logger($selectedMoviesID);

        if ($response->successful()) {
            $resultado = $response->json();
            $recomendaciones = $resultado['recomendaciones'];
            $invalidIds = $resultado['invalid_ids'];
            $validIds = $resultado['valid_ids'];

            // Si hay IDs no válidos, registrar en el log y opcionalmente notificar al usuario
            if (!empty($invalidIds)) {
                logger('Advertencia: Algunas películas no están en el dataset:', $invalidIds);
                // Opcional: Agregar mensaje para el usuario
                return [
                    'recomendaciones' => $recomendaciones,
                    'invalid_ids' => $invalidIds,
                    'valid_ids' => $validIds,
                ];
            }

            return $recomendaciones;
        } else {
            // Extraer la parte JSON del string eliminando el código de error "404: "
            $jsonString = substr($response["detail"], strpos($response["detail"], "{"));

            // Decodificar el JSON a un array PHP
            $decodedResponse = json_decode(str_replace("'", '"', $jsonString), true);

            // dd($jsonString);

            logger('Error en la respuesta de la API:', [
                'status' => $response->status(),
                'body' => $response->body(),
                'invalid_ids' => $decodedResponse['invalid_ids'],
            ]);
            return [
                'error' => 'Error al obtener recomendaciones: ' . $response->status(),
                'detalle' => $response->json(),
                'invalid_ids' => $decodedResponse['invalid_ids'],
            ];
        }
    }

    public function estadisticas()
    {
        // Enviar solicitud a la API de Python para estadísticas
        $response = Http::withoutVerifying()->get('http://localhost:8000/estadisticas');

        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json([
                'error' => 'Error al obtener estadísticas: ' . $response->status(),
                'detalle' => $response->json(),
            ], $response->status());
        }
    }
}
