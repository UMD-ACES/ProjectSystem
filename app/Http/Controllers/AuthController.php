<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Subfission\Cas\Facades\Cas;

class AuthController extends Controller
{
    public function logout(Request $request)
    {
        Auth::guard()->logout();
        Auth::logout();
        Cas::logout();

        $request->session()->invalidate();
    }
}
