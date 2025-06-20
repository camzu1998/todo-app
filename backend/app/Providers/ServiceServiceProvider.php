<?php

namespace App\Providers;

use App\Services\Interfaces\TaskServiceInterface;
use App\Services\TaskService;
use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TaskServiceInterface::class,
            TaskService::class
        );
    }

    public function boot(): void
    {
        //
    }
}
