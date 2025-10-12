<?php

namespace Tests\Feature\Todo;

use App\Enums\Color;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\TestDox;

class UpdateControllerTest extends TestCase
{
    use RefreshDatabase;

    #[TestDox('日付と時刻を指定してTODOを更新できる')]
    public function test_authenticated_user_can_update_todo_with_date_and_time(): void
    {
        $user = User::factory()->create();

        $todoId = DB::table('todos')->insertGetId([
            'title' => 'Original title',
            'memo' => 'Original memo',
            'deadline' => '2025-01-01 08:00:00',
            'color' => Color::Red->value,
            'done' => false,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put(route('todo.update'), [
            'id' => $todoId,
            'title' => 'Updated title',
            'memo' => 'Updated memo',
            'date' => '2025-01-02',
            'time' => '10:15',
            'color' => Color::Green->value,
        ]);

        $response->assertRedirect(route('todo.index'));

        $this->assertDatabaseHas('todos', [
            'id' => $todoId,
            'title' => 'Updated title',
            'memo' => 'Updated memo',
            'deadline' => '2025-01-02 10:15',
            'color' => Color::Green->value,
        ]);
    }

    #[TestDox('時刻のみ更新すると当日の日付が補完される')]
    public function test_time_only_update_uses_current_date(): void
    {
        $user = User::factory()->create();

        $todoId = DB::table('todos')->insertGetId([
            'title' => 'Original',
            'memo' => null,
            'deadline' => null,
            'color' => null,
            'done' => false,
            'user_id' => $user->id,
        ]);

        $today = date('Y-m-d');

        $response = $this->actingAs($user)->put(route('todo.update'), [
            'id' => $todoId,
            'title' => 'Updated',
            'memo' => null,
            'date' => null,
            'time' => '09:00',
            'color' => null,
        ]);

        $response->assertRedirect(route('todo.index'));

        $this->assertDatabaseHas('todos', [
            'id' => $todoId,
            'deadline' => "{$today} 09:00",
        ]);
    }

    #[TestDox('タイトル未入力の更新はバリデーションエラーになる')]
    public function test_validation_error_when_title_missing(): void
    {
        $user = User::factory()->create();

        $todoId = DB::table('todos')->insertGetId([
            'title' => 'Original',
            'memo' => null,
            'deadline' => null,
            'color' => null,
            'done' => false,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->from(route('todo.edit', ['id' => $todoId]))->put(route('todo.update'), [
            'id' => $todoId,
            'title' => '',
            'memo' => null,
            'date' => null,
            'time' => null,
            'color' => null,
        ]);

        $response
            ->assertRedirect(route('todo.edit', ['id' => $todoId]))
            ->assertSessionHasErrors(['title']);

        $this->assertDatabaseHas('todos', [
            'id' => $todoId,
            'title' => 'Original',
        ]);
    }

    #[TestDox('未ログイン利用者は更新時にログイン画面へリダイレクトされる')]
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->put(route('todo.update'), [
            'id' => 1,
            'title' => 'Guest',
            'memo' => null,
            'date' => null,
            'time' => null,
            'color' => null,
        ]);

        $response->assertRedirect(route('login'));
    }
}
