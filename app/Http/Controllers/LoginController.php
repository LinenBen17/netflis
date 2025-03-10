<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $userName = $request->input('username');

        session(['userSession' => $userName]);
        session(['movieInterest' => ['A']]);
        session(['movieInterestID' => [1]]);

        return redirect()->route('movies.index');
    }
}
