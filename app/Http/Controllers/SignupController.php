<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SignupController extends Controller
{
    public function __invoke()
    {
        return view('signup');
    }
}
