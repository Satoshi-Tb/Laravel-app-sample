<?php

namespace Tests\Feature\Todo;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class IndexControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_sees_sorted_todos(): void
    {
        $user = User::factory()->create();

        DB::table('todos')->insert([
            [
                'title' => 'Done later',
                'memo' => null,
                'deadline' => '2025-01-05 09:00:00',
                'color' => null,
                'done' => true,
                'user_id' => $user->id,
            ],
            [
                'title' => 'Undone later',
                'memo' => null,
                'deadline' => '2025-01-10 09:00:00',
                'color' => null,
                'done' => false,
                'user_id' => $user->id,
            ],
            [
                'title' => 'Undone sooner',
                'memo' => null,
                'deadline' => '2025-01-02 09:00:00',
                'color' => null,
                'done' => false,
                'user_id' => $user->id,
            ],
        ]);

        $response = $this->actingAs($user)->get(route('todo.index'));

        $response
            ->assertOk()
            ->assertViewIs('todo.index')
            ->assertViewHas('emptyMessage', null)
            ->assertViewHas('todos', function ($todos) {
                $titles = $todos->pluck('title')->all();

                return $titles === [
                    'Undone sooner',
                    'Undone later',
                    'Done later',
                ];
            });
    }

    public function test_authenticated_user_sees_empty_message_when_no_todos(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('todo.index'));

        $response
            ->assertOk()
            ->assertViewIs('todo.index')
            ->assertViewHas('todos', function ($todos) {
                return $todos->isEmpty();
            })
            ->assertViewHas('emptyMessage', 'TODOが登録されていません。');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('todo.index'));

        $response->assertRedirect(route('login'));
    }
}
