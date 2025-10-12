<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Repositories\TodoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\TestDox;

class TodoRepositoryTest extends TestCase
{
    use RefreshDatabase;

    #[TestDox('listメソッドはユーザー別に絞り込み未完了優先でソートする')]
    public function test_list_orders_by_done_and_deadline_and_filters_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        DB::table('todos')->insert([
            [
                'title' => 'User 1 done later',
                'memo' => null,
                'deadline' => '2025-01-10 09:00:00',
                'color' => null,
                'done' => true,
                'user_id' => $user->id,
            ],
            [
                'title' => 'User 1 undone later',
                'memo' => null,
                'deadline' => '2025-01-05 09:00:00',
                'color' => null,
                'done' => false,
                'user_id' => $user->id,
            ],
            [
                'title' => 'User 1 undone sooner',
                'memo' => null,
                'deadline' => '2025-01-02 09:00:00',
                'color' => null,
                'done' => false,
                'user_id' => $user->id,
            ],
            [
                'title' => 'Other user todo',
                'memo' => null,
                'deadline' => '2025-01-01 09:00:00',
                'color' => null,
                'done' => false,
                'user_id' => $otherUser->id,
            ],
        ]);

        $repository = new TodoRepository();

        $todos = $repository->list($user->id);

        $this->assertSame(
            [
                'User 1 undone sooner',
                'User 1 undone later',
                'User 1 done later',
            ],
            $todos->pluck('title')->all()
        );
    }
}
