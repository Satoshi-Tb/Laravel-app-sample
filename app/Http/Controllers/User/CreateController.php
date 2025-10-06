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
                'password' => Hash::make($data['password']),
            ]);

        $logined = Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        if ($logined === true) {
            return redirect()->route('todo.index');
        }

        return redirect()->route('signup');
    }

}
