<?php

namespace App\Services\Interfaces;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;

interface TaskServiceInterface
{
    public function create(StoreTaskRequest $request): Task;

    public function update(UpdateTaskRequest $request, int $id): Task;

    public function delete(int $id): void;
}
