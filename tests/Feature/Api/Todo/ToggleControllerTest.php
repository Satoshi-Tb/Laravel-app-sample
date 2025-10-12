<?php

namespace Tests\Feature\Api\Todo;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\TestDox;

class ToggleControllerTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/api/todo/toggle';

    #[TestDox('認証済みユーザーは完了状態を更新できる')]
    public function test_authenticated_user_can_toggle_done_state(): void
    {
        $user = User::factory()->create();

        $todoId = DB::table('todos')->insertGetId([
            'title' => 'Test todo',
            'memo' => null,
            'deadline' => null,
            'color' => '#FFFFFF',
            'done' => false,
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson(self::ENDPOINT, [
            'id' => $todoId,
            'done' => true,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('todos', [
            'id' => $todoId,
            'done' => true,
        ]);
    }

    #[TestDox('未認証ユーザーはトグルAPIへアクセスできない')]
    public function test_request_requires_authenticated_user(): void
    {
        $response = $this->putJson(self::ENDPOINT, [
            'id' => 1,
            'done' => true,
        ]);

        $response->assertUnauthorized();
    }

    #[TestDox('不正なリクエストはバリデーションエラーとなり状態が変更されない')]
    public function test_validation_errors_are_returned_for_invalid_payload(): void
    {
        $user = User::factory()->create();

        $todoId = DB::table('todos')->insertGetId([
            'title' => 'Another todo',
            'memo' => null,
            'deadline' => null,
            'color' => '#FFFFFF',
            'done' => false,
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson(self::ENDPOINT, [
            'id' => $todoId,
            'done' => 'invalid',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['done']);

        $this->assertDatabaseHas('todos', [
            'id' => $todoId,
            'done' => false,
        ]);
    }
}
