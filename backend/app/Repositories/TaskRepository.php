<?php

namespace App\Repositories;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository extends BaseRepository implements TaskRepositoryInterface
{
    public function __construct(Task $model)
    {
        parent::__construct($model);
    }

    public function findByStatus(TaskStatus $status): Collection
    {
        return $this->model->where('status', $status->value)->get();
    }

    public function findCompleted(): Collection
    {
        return $this->findByStatus(TaskStatus::COMPLETED);
    }

    public function findPending(): Collection
    {
        return $this->findByStatus(TaskStatus::PENDING);
    }

    public function markAsCompleted(int $id): bool
    {
        return $this->update($id, ['status' => TaskStatus::COMPLETED->value]);
    }

    public function getWithPagination(int $perPage = 10)
    {
        return $this->model
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
