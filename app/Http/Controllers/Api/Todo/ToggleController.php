<?php

namespace App\Http\Controllers\Api\Todo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Todo\ToggleRequest;
use App\Repositories\Repository;
use Illuminate\Http\Response;

class ToggleController extends Controller
{
    public function __construct(
        protected Repository $repository,
    )
    {}

    public function __invoke(ToggleRequest $request)
    {
        $data = $request->validated();

        $this->repository->update($data['id'], [
            'done' => $data['done'],
        ]);

        return response(status: Response::HTTP_OK);
    }
}
