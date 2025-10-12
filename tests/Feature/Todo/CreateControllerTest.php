<?php

namespace Tests\Feature\Todo;

use App\Enums\Color;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateControllerTest extends TestCase
{
    use RefreshDatabase;

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
