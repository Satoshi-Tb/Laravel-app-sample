<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function __construct(protected Repository $repository)
    {
    }

    public function __invoke()
    {
        $user = Auth::user();
        if ($user === null) {
            return redirect()->route('login');
        }

        $todos = $this->repository->list($user->id);

        $emptyMessage = $todos->isEmpty() ? 'TODOが登録されていません。' : null;

        return view('todo.index', [
            'todos' => $todos,
            'emptyMessage' => $emptyMessage,
        ]);
    }
}
