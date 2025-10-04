<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Todo\DeleteRequest;
use App\Repositories\Repository;

class DeleteController extends Controller
{
    public function __construct(protected Repository $repository)
    {
    }

    public function __invoke(DeleteRequest $request)
    {
        $data = $request->validated();

        $id = $data['id'];

        $this->repository->delete($id);

        return redirect()->route('todo.index');
    }
}
