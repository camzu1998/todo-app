<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use App\Enums\TaskStatus;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TaskRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TaskRepository(new Task());
    }

    public function test_can_create_task(): void
    {
        $data = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => TaskStatus::PENDING->value
        ];

        $task = $this->repository->create($data);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Test Task', $task->title);
        $this->assertDatabaseHas('tasks', $data);
    }

    public function test_can_find_task_by_id(): void
    {
        $task = Task::factory()->create();

        $found = $this->repository->find($task->id);

        $this->assertInstanceOf(Task::class, $found);
        $this->assertEquals($task->id, $found->id);
    }

    public function test_can_update_task(): void
    {
        $task = Task::factory()->create();
        $updateData = ['title' => 'Updated Title'];

        $result = $this->repository->update($task->id, $updateData);

        $this->assertTrue($result);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title'
        ]);
    }

    public function test_can_delete_task(): void
    {
        $task = Task::factory()->create();

        $result = $this->repository->delete($task->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_can_find_by_status(): void
    {
        Task::factory()->count(3)->create(['status' => TaskStatus::PENDING->value]);
        Task::factory()->count(2)->create(['status' => TaskStatus::COMPLETED->value]);

        $pendingTasks = $this->repository->findByStatus(TaskStatus::PENDING);

        $this->assertCount(3, $pendingTasks);
    }

    public function test_can_mark_as_completed(): void
    {
        $task = Task::factory()->create();

        $result = $this->repository->markAsCompleted($task->id);

        $this->assertTrue($result);
        $this->assertEquals(TaskStatus::COMPLETED->value, $task->fresh()->status->value);
    }
}
