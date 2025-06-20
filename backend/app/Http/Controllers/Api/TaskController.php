<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Services\Interfaces\TaskServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function __construct(
        private TaskServiceInterface $taskService,
        private TaskRepositoryInterface $taskRepository
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $tasks = $this->taskRepository->all();

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->create($request);

        return response()->json([
            'data' => new TaskResource($task),
            'message' => 'Zadanie zostało utworzone'
        ], 201);
    }

    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        $task = $this->taskService->update($request, $id);

        return response()->json([
            'data' => new TaskResource($task),
            'message' => 'Zadanie zostało zaktualizowane'
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->taskService->delete($id);

        return response()->json([
            'message' => 'Zadanie zostało usunięte'
        ], 204);
    }
}
