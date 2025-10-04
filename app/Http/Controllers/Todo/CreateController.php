<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Todo\CreateRequest;
use App\Repositories\Repository;

class CreateController extends Controller
{
    public function __construct(protected Repository $repository)
    {
    }

    public function __invoke(CreateRequest $request)
    {
        $data = $request->validated();

        $date = $data['date'];
        $time = $data['time'];

        $datetime = null;

        if ($date !== null && $time !== null) {
            $datetime = "{$date} {$time}";
        }

        $this->repository->add([
            'title' => $data['title'],
            'memo' => $data['memo'],
            'deadline' => $datetime,
            'color' => $data['color'],
            'user_id' => 1,
        ]);

        return redirect()->route('todo.index');
    }
}
