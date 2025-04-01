<?php

namespace App\Services;

use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Http;

class netflisApiService
{
    public function recommend($selectedMoviesID)
    {
        try {
            // Enviar solicitud a la API de Python para recomendaciones
            $response = Http::withoutVerifying()
                ->timeout(10)
                ->post('http://localhost:5000/recomendar', [
                    'movie_ids' => $selectedMoviesID,
                    'num_recomendaciones' => 10,
                ]);

            Logger('Solicitud enviada a FastAPI', ['movie_ids' => $selectedMoviesID]);

            if ($response->successful()) {
                $resultado = $response->json();
                $recomendaciones = $resultado['recomendaciones'] ?? [];
                $invalidIds = $resultado['invalid_ids'] ?? [];
                $validIds = $resultado['valid_ids'] ?? [];

                // Si hay IDs no válidos, registrar en el log
                if (!empty($invalidIds)) {
                    Logger('Advertencia: Algunas películas no están en el dataset', ['invalid_ids' => $invalidIds]);
                    return [
                        'recomendaciones' => $recomendaciones,
                        'invalid_ids' => $invalidIds,
                        'valid_ids' => $validIds,
                    ];
                }

                return $recomendaciones;
            } else {
                // Obtener el cuerpo de la respuesta
                $body = $response->body();
                Logger('Error en la respuesta de la API', [
                    'status' => $response->status(),
                    'body' => $body,
                ]);

                // Intentar decodificar el JSON del cuerpo
                $decodedResponse = json_decode($body, true);

                // Verificar si la decodificación fue exitosa y si contiene 'invalid_ids'
                $invalidIds = [];
                $validIds = [];
                if (is_array($decodedResponse) && isset($decodedResponse['invalid_ids'])) {
                    $invalidIds = $decodedResponse['invalid_ids'];
                    $validIds = $decodedResponse['valid_ids'] ?? [];
                } elseif (is_array($decodedResponse) && isset($decodedResponse['detail']) && is_array($decodedResponse['detail'])) {
                    $invalidIds = $decodedResponse['detail']['invalid_ids'] ?? [];
                    $validIds = $decodedResponse['detail']['valid_ids'] ?? [];
                }

                return [
                    'error' => 'Error al obtener recomendaciones: ' . $response->status(),
                    'detalle' => $decodedResponse ?: $body,
                    'invalid_ids' => $invalidIds,
                    'valid_ids' => $validIds,
                ];
            }
        } catch (\Exception $e) {
            Logger('Excepción al conectar con FastAPI', [
                'message' => $e->getMessage(),
                'movie_ids' => $selectedMoviesID,
            ]);
            return [
                'error' => 'Error al conectar con la API de recomendaciones',
                'detalle' => $e->getMessage(),
                'invalid_ids' => [],
                'valid_ids' => [],
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
