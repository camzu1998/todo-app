<?php

namespace App\Services;

use App\Dto\TaskDto;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Services\Interfaces\TaskServiceInterface;

class TaskService implements TaskServiceInterface
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {}

    public function create(StoreTaskRequest $request): Task
    {
        $dto = TaskDto::fromStoreRequest($request);

        return $this->taskRepository->create($dto->toArray());
    }

    public function update(UpdateTaskRequest $request, int $id): Task
    {
        $task = $this->taskRepository->find($id);

        if (!$task) {
            throw new \Exception('Zadanie nie zostało znalezione', 404);
        }

        $dto = TaskDto::fromUpdateRequest($request, $task);
        $this->taskRepository->update($id, $dto->toArray());

        return $this->taskRepository->find($id);
    }

    public function delete(int $id): void
    {
        $task = $this->taskRepository->find($id);

        if (!$task) {
            throw new \Exception('Zadanie nie zostało znalezione', 404);
        }

        $this->taskRepository->delete($id);
    }
}
