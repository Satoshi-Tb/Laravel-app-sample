<?php

namespace App\Http\Controllers;

class LoginController extends Controller
{
    public function __invoke()
    {
        return view('login');
    }
}
