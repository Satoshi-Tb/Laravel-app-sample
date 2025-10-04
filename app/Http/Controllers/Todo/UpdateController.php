<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Todo\UpdateRequest;
use App\Repositories\Repository;

class UpdateController extends Controller
{
    public function __construct(protected Repository $repository)
    {
    }

    public function __invoke(UpdateRequest $request)
    {
        $data = $request->validated();

        $date = $data['date'];
        $time = $data['time'];

        $datetime = null;

        if ($date !== null && $time !== null) {
            $datetime = "{$date} {$time}";
        }

        $this->repository->update($data['id'], [
            'title' => $data['title'],
            'memo' => $data['memo'],
            'deadline' => $datetime,
            'color' => $data['color'],
        ]);

        return redirect()->route('todo.index');
    }
}
