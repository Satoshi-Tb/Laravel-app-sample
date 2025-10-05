<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $data = $request->validated();

        $logined = Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        if ($logined === true) {
            $request->session()->regenerate();
            return redirect()->intended(route('todo.index'));
        }

        return back()->withErrors([
            'login' => 'メールアドレスまたはパスワードが間違っています',
        ]);
    }
}
