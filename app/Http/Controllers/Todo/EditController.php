<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Repositories\Repository;
use Carbon\Carbon;

class EditController extends Controller
{
    public function __construct(protected Repository $repository)
    {
    }

    public function __invoke(int $id)
    {
        $todo = $this->repository->find($id);

        if ($todo === null) {
            return redirect()->route('todo.index');
        }

        $date = Carbon::create($todo->deadline);

        return view('todo.edit', ['todo' => (object) [
            'id' => $todo->id,
            'title' => $todo->title,
            'color' => $todo->color,
            'done' => $todo->done,
            'date' => $date->isoFormat('YYYY-MM-DD'),
            'time' => $date->isoFormat('HH:mm'),
            'memo' => $todo->memo,
        ]]);
    }
}
