<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface Repository
{
    public function find(int $id): \stdClass|null;

    public function list(int $userId): Collection;

    public function add(\stdClass|array $data): void;

    public function update(int $id, \stdClass|array $data): void;

    public function delete(int $id): void;
}
