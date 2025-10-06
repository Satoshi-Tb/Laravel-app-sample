<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        /**
        * @var \App\Models\User $user
        */
        $user = Auth::user();
        $user->tokens()->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $cookie = Cookie::forget('API_TOKEN');

        return redirect()
           ->route('login')
           ->withCookie($cookie);
    }
}
