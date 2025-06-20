<?php

namespace App\Repositories\Interfaces;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function findByStatus(TaskStatus $status): Collection;

    public function findCompleted(): Collection;

    public function findPending(): Collection;

    public function markAsCompleted(int $id): bool;

    public function getWithPagination(int $perPage = 10);
}
