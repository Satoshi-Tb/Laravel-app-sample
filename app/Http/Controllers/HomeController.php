<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __invoke()
    {
        if (Auth::check()) {
            return redirect()->route('todo.index');
        }

        return view('home');
    }
}
