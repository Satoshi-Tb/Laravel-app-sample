<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Repository;

class IndexController extends Controller
{
    public function __construct(protected Repository $repository)
    {
    }

    public function __invoke()
    {
        $todos = $this->repository->list(1)->sortBy("deadline");

        return view('todo.index', [
            'todos' => $todos,
        ]);
    }
}
