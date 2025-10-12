<?php

namespace Tests\Feature\Todo;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EditControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_edit_form_with_formatted_datetime(): void
    {
        $user = User::factory()->create();

        $todoId = DB::table('todos')->insertGetId([
            'title' => 'Editable',
            'memo' => 'Details',
            'deadline' => '2025-03-01 14:45:00',
            'color' => '#FFFFFF',
            'done' => false,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('todo.edit', ['id' => $todoId]));

        $response
            ->assertOk()
            ->assertViewIs('todo.edit')
            ->assertViewHas('todo', function ($todo) use ($todoId) {
                return $todo->id === $todoId
                    && $todo->date === '2025-03-01'
                    && $todo->time === '14:45'
                    && $todo->title === 'Editable'
                    && $todo->memo === 'Details';
            });
    }

    public function test_missing_todo_redirects_to_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('todo.edit', ['id' => 999]));

        $response->assertRedirect(route('todo.index'));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('todo.edit', ['id' => 1]));

        $response->assertRedirect(route('login'));
    }
}
