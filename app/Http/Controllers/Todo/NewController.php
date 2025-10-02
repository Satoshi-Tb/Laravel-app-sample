<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Repositories\Repository;
use Illuminate\Http\Request;

class NewController extends Controller
{
    public function __construct(protected Repository $repository)
    {
    }

    public function __invoke()
    {
        return view('todo.new');
    }
}
