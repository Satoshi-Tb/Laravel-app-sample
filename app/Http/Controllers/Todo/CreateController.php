<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Todo\CreateRequest;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{
    public function __construct(protected Repository $repository)
    {
    }

    public function __invoke(CreateRequest $request)
    {
        $user = Auth::user();
        if ($user === null) {
            return redirect()->route('login');
        }

        $data = $request->validated();

        $date = $data['date'];
        $time = $data['time'];

        $datetime = null;

        // timeがnullの時は、time を00:00として扱う
        if ($date !== null) {
            if ($time === null) {
                $time = '00:00';
            }
            $datetime = "{$date} {$time}";
        } elseif ($time !== null) {
            // dateがnullの時は、dateを今日の日付として扱う
            $date = date('Y-m-d');
            $datetime = "{$date} {$time}";
        }

        $this->repository->add([
            'title' => $data['title'],
            'memo' => $data['memo'],
            'deadline' => $datetime,
            'color' => $data['color'],
            'user_id' => $user->id,
        ]);

        return redirect()->route('todo.index');
    }
}
