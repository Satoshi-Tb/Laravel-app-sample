<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $data = $request->validated();

        // Laravel の Auth::attempt はパスワードをハッシュ化して比較するため、今回は使わない
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
