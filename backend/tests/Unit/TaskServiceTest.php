<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use App\Enums\TaskStatus;
use App\Services\TaskService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Mockery;
use Mockery\MockInterface;

class TaskServiceTest extends TestCase
{
    private TaskService $service;
    private MockInterface $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = Mockery::mock(TaskRepositoryInterface::class);
        $this->service = new TaskService($this->repositoryMock);
    }

    public function test_can_create_task(): void
    {
        $requestMock = Mockery::mock(StoreTaskRequest::class);
        $requestMock->shouldReceive('validated')
            ->with('title')
            ->andReturn('Test Task');
        $requestMock->shouldReceive('validated')
            ->with('description')
            ->andReturn('Test Description');
        $requestMock->shouldReceive('validated')
            ->with('status')
            ->andReturn(TaskStatus::PENDING->value);

        $expectedTask = new Task([
            'id' => 1,
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => TaskStatus::PENDING->value
        ]);

        $this->repositoryMock
            ->shouldReceive('create')
            ->once()
            ->with([
                'title' => 'Test Task',
                'description' => 'Test Description',
                'status' => TaskStatus::PENDING->value
            ])
            ->andReturn($expectedTask);

        $result = $this->service->create($requestMock);

        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals('Test Task', $result->title);
    }

    public function test_can_update_task(): void
    {
        $taskId = 1;
        $existingTask = new Task([
            'id' => $taskId,
            'title' => 'Old Title',
            'description' => 'Old Description',
            'status' => TaskStatus::PENDING->value
        ]);

        $requestMock = Mockery::mock(UpdateTaskRequest::class);
        $requestMock->shouldReceive('validated')
            ->with('title')
            ->andReturn('Updated Title');
        $requestMock->shouldReceive('validated')
            ->with('description')
            ->andReturn(null);
        $requestMock->shouldReceive('validated')
            ->with('status')
            ->andReturn(null);

        $this->repositoryMock
            ->shouldReceive('find')
            ->with($taskId)
            ->twice()
            ->andReturn($existingTask);

        $this->repositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($taskId, [
                'title' => 'Updated Title',
                'description' => 'Old Description',
                'status' => TaskStatus::PENDING->value
            ])
            ->andReturn(true);

        $result = $this->service->update($requestMock, $taskId);

        $this->assertInstanceOf(Task::class, $result);
    }

    public function test_throws_exception_when_updating_non_existent_task(): void
    {
        $requestMock = Mockery::mock(UpdateTaskRequest::class);

        $this->repositoryMock
            ->shouldReceive('find')
            ->with(999)
            ->once()
            ->andReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Zadanie nie zostało znalezione');

        $this->service->update($requestMock, 999);
    }

    public function test_can_delete_task(): void
    {
        $taskId = 1;
        $task = new Task(['id' => $taskId]);

        $this->repositoryMock
            ->shouldReceive('find')
            ->with($taskId)
            ->once()
            ->andReturn($task);

        $this->repositoryMock
            ->shouldReceive('delete')
            ->with($taskId)
            ->once()
            ->andReturn(true);

        $this->service->delete($taskId);

        $this->assertTrue(true); // Assert no exception thrown
    }

    public function test_throws_exception_when_deleting_non_existent_task(): void
    {
        $this->repositoryMock
            ->shouldReceive('find')
            ->with(999)
            ->once()
            ->andReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Zadanie nie zostało znalezione');

        $this->service->delete(999);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
