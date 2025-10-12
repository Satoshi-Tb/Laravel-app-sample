<?php

namespace Tests\Feature\Todo;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\TestDox;

class DeleteControllerTest extends TestCase
{
    use RefreshDatabase;

    #[TestDox('認証済みユーザーはTODOを削除できる')]
    public function test_authenticated_user_can_delete_todo(): void
    {
        $user = User::factory()->create();

        $todoId = DB::table('todos')->insertGetId([
            'title' => 'Delete me',
            'memo' => null,
            'deadline' => null,
            'color' => null,
            'done' => false,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('todo.delete'), [
            'id' => $todoId,
        ]);

        $response->assertRedirect(route('todo.index'));

        $this->assertDatabaseMissing('todos', [
            'id' => $todoId,
        ]);
    }

    #[TestDox('IDが未指定の場合はバリデーションエラーになる')]
    public function test_validation_error_when_id_missing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from(route('todo.index'))->delete(route('todo.delete'), [
            'id' => null,
        ]);

        $response
            ->assertRedirect(route('todo.index'))
            ->assertSessionHasErrors(['id']);
    }

    #[TestDox('未ログイン利用者は削除時にログイン画面へリダイレクトされる')]
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->delete(route('todo.delete'), [
            'id' => 1,
        ]);

        $response->assertRedirect(route('login'));
    }
}
