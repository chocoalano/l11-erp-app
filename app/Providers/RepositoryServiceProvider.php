<?php

namespace App\Providers;

use App\Interfaces\AttendanceInterface;
use App\Interfaces\AuthenticationInterface;
use App\Interfaces\JobPositionInterface;
use App\Interfaces\OrganizationInterface;
use App\Interfaces\UserInterface;

use App\Repositories\AttendanceRepository;
use App\Repositories\AuthenticationRepository;
use App\Repositories\JobPositionRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\UserRepository;
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
        $this->app->bind(UserInterface::class,UserRepository::class);
        $this->app->bind(OrganizationInterface::class,OrganizationRepository::class);
        $this->app->bind(JobPositionInterface::class,JobPositionRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
