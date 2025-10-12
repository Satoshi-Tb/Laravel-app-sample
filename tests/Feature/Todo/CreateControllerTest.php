<?php

namespace Tests\Feature\Todo;

use App\Enums\Color;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\TestDox;

class CreateControllerTest extends TestCase
{
    use RefreshDatabase;

    #[TestDox('日付のみ指定したTODOを登録できる')]
    public function test_authenticated_user_can_create_todo_with_date_only(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('todo.create'), [
            'title' => 'Date only todo',
            'memo' => 'memo',
            'date' => '2025-01-10',
            'time' => null,
            'color' => Color::Blue->value,
        ]);

        $response->assertRedirect(route('todo.index'));

        $this->assertDatabaseHas('todos', [
            'title' => 'Date only todo',
            'memo' => 'memo',
            'deadline' => '2025-01-10 00:00',
            'color' => Color::Blue->value,
            'user_id' => $user->id,
        ]);
    }

    #[TestDox('時刻のみ指定したTODOは当日の日付で保存される')]
    public function test_authenticated_user_can_create_todo_with_time_only(): void
    {
        $user = User::factory()->create();

        $today = date('Y-m-d');

        $response = $this->actingAs($user)->post(route('todo.create'), [
            'title' => 'Time only todo',
            'memo' => null,
            'date' => null,
            'time' => '15:30',
            'color' => null,
        ]);

        $response->assertRedirect(route('todo.index'));

        $this->assertDatabaseHas('todos', [
            'title' => 'Time only todo',
            'deadline' => "{$today} 15:30",
            'user_id' => $user->id,
        ]);
    }

    #[TestDox('タイトル未入力はバリデーションエラーになる')]
    public function test_validation_error_when_title_missing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from(route('todo.new'))->post(route('todo.create'), [
            'title' => '',
            'memo' => 'memo',
            'date' => null,
            'time' => null,
            'color' => null,
        ]);

        $response
            ->assertRedirect(route('todo.new'))
            ->assertSessionHasErrors(['title']);

        $this->assertDatabaseCount('todos', 0);
    }

    #[TestDox('未ログイン利用者はログイン画面へリダイレクトされる')]
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->post(route('todo.create'), [
            'title' => 'Guest todo',
            'memo' => null,
            'date' => null,
            'time' => null,
            'color' => null,
        ]);

        $response->assertRedirect(route('login'));
    }
}
