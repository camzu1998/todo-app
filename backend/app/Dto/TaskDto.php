<?php

namespace App\Dto;

use App\Enums\TaskStatus;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;

class TaskDto
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description = null,
        public readonly TaskStatus $status = TaskStatus::PENDING,
    ) {}

    public static function fromStoreRequest(StoreTaskRequest $request): self
    {
        return new self(
            title: $request->validated('title'),
            description: $request->validated('description'),
            status: $request->validated('status')
                ? TaskStatus::from($request->validated('status'))
                : TaskStatus::PENDING,
        );
    }

    public static function fromUpdateRequest(UpdateTaskRequest $request, Task $task): self
    {
        return new self(
            title: $request->validated('title') ?? $task->title,
            description: $request->validated('description') ?? $task->description,
            status: $request->validated('status')
                ? TaskStatus::from($request->validated('status'))
                : $task->status,
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status->value,
        ];
    }
}
