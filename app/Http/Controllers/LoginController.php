<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function login(Request $request)
    {
        $userName = $request->input('username');

        // Regenerar la sesión para evitar conflictos con sesiones previas
        session()->regenerate();

        // Establecer datos de sesión
        session(['userSession' => $userName]);
        session(['movieInterest' => []]);
        session(['movieInterestID' => []]);
        session(['unique_user_id' => $userName . '-' . \Illuminate\Support\Str::uuid()]); // Inicializar user_id

        return redirect()->route('movies.index');
    }

    public function logout(Request $request)
    {
        session()->forget('userSession');
        session()->forget('movieInterest');
        session()->forget('movieInterestID');

        return view('login');
    }
}
