<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TodoRepository implements Repository
{
    // テーブル名称
    protected string $tableName = 'todos';


    /**
     * 指定されたIDのTODOを1件返す
     * TODOが存在しない場合はnullを返す
     *
     * @param integer $id
     * @return \stdClass|null
     */
    public function find(int $id): \stdClass|null
    {
        return DB::table($this->tableName)->where('id', $id)->first();
    }

    /**
     * 指定されたユーザーIDのTODO一覧を返す
     *
     * @param integer $userId
     * @return Collection
     */
    public function list(int $userId): Collection
    {
        return DB::table($this->tableName)->where('user_id', $userId)->get();
    }

    /**
     * 指定された1件のTODO、またはTODOの配列を追加する
     *
     * @param \stdClass|array $data
     * @return void
     */
    public function add(\stdClass|array $data): void
    {
        DB::table($this->tableName)->insert((array) $data);
    }


    /**
     * 指定されたIDのTODOを更新する
     *
     * @param integer $id
     * @param \stdClass|array $data
     * @return void
     */
    public function update(int $id, \stdClass|array $data): void
    {
        DB::table($this->tableName)->where('id', $id)->update((array) $data);
    }

    /**
     * 指定されたIDのTODOを削除する
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id): void
    {
        DB::table($this->tableName)->delete($id);
    }
}
