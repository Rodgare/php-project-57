<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\TaskStatus;
use App\Models\User;

class TaskStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

    }

    public function test_it_can_display_a_list_of_task_statuses(): void
    {
        TaskStatus::factory()->count(3)->create();
        $response = $this->get('/task_statuses');

        $response->assertStatus(200);
        $response->assertViewIs('task_statuses.index');
        $response->assertViewHas('task_statuses');
        $response->assertSee('Статус Задачи 1');
    }

    public function test_it_can_create_a_task_status(): void
    {
        $data = ['name' => 'Новый Статус'];

        $response = $this->post('/task-statuses', $data);
        $response->assertStatus(302);
        $response->assertRedirect('/task-statuses');
        $this->assertDatabaseHas('task_statuses', $data);
        $response->assertSessionHasNoErrors();
    }

    public function test_it_cannot_create_a_task_status_with_missing_name(): void
    {
        $data = ['name' => ''];

        $response = $this->post('/task-statuses', $data);
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('task_statuses', $data);
    }

    public function test_it_cannot_create_a_task_status_with_duplicate_name(): void
    {
        TaskStatus::factory()->create(['name' => 'Existing Status']);
        $data = ['name' => 'Existing Status'];

        $response = $this->post('/task-statuses', $data);
        $response->assertSessionHasErrors('name');
    }

    public function test_it_can_update_a_task_status()
    {
        $status = TaskStatus::factory()->create(['name' => 'Original Name']);
        $updatedData = ['name' => 'Updated Name'];

        $response = $this->patch('/task-statuses/' . $status->id, $updatedData);

        $response->assertStatus(302);
        $response->assertRedirect('/task-statuses');
        $this->assertDatabaseHas('task_statuses', [
            'id' => $status->id,
            'name' => 'Updated Name'
        ]);
        $response->assertSessionHasNoErrors();
    }

    public function test_it_cannot_update_a_task_status_with_duplicate_name()
    {
        $existingStatus = TaskStatus::factory()->create(['name' => 'Existing Name']);
        $statusToUpdate = TaskStatus::factory()->create(['name' => 'Another Name']);
        $updatedData = ['name' => 'Existing Name'];

        $response = $this->patch('/task-statuses/' . $statusToUpdate->id, $updatedData);
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseHas('task_statuses', [
            'id' => $statusToUpdate->id,
            'name' => 'Another Name'
        ]);
    }

    public function test_it_can_delete_a_task_status()
    {
        $status = TaskStatus::factory()->create();
        $response = $this->delete('/task-statuses/' . $status->id);
        $response->assertStatus(302);
        $response->assertRedirect('/task-statuses');
        $this->assertDatabaseMissing('task_statuses', ['id' => $status->id]);
    }
}
