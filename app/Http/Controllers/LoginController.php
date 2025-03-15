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

        session(['userSession' => $userName]);
        session(['movieInterest' => []]);
        session(['movieInterestID' => []]);

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
