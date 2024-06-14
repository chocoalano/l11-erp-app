<?php

namespace App\Providers;

use App\Interfaces\AttendanceInterface;
use App\Interfaces\AuthenticationInterface;
use App\Repositories\AttendanceRepository;
use App\Repositories\AuthenticationRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AuthenticationInterface::class,AuthenticationRepository::class);
        $this->app->bind(AttendanceInterface::class,AttendanceRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
