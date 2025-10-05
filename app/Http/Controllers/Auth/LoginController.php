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
        // $logined = Auth::attempt([
        //     'email' => $data['email'],
        //     'password' => $data['password'],
        // ]);

        $user = DB::table('users')
            ->where('email', $data['email'])
            ->first();

        $logined = $user !== null
            // `hash_equals` を使うことで比較時間が一定になり、タイミング攻撃を避けられる
            && hash_equals((string) $user->password, (string) $data['password']);

        if ($logined === true) {
            // `attempt` を使わず自前で認証したので、ここで明示的にログイン状態へ
            Auth::loginUsingId($user->id);

            $request->session()->regenerate();

            return redirect()->intended(route('todo.index'));
        }

        return back()->withErrors([
            'login' => 'メールアドレスまたはパスワードが間違っています',
        ]);
    }
}
