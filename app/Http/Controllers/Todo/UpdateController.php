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

    }
}
