<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateController extends Controller
{
    public function __invoke(CreateRequest $request)
    {
        $data = $request->validated();

        DB::table('users')
            ->insert([
                'name' => $data['username'],
                'email' => $data['email'],
                // 'password' => Hash::make($data['password']),
                'password' => $data['password'], // サンプルアプリのため、ハッシュ化しない
            ]);

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

            return redirect()->route('todo.index');
        }

        return redirect()->route('signup');
    }

}
