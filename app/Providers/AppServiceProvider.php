<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Task;
use App\Observers\TaskObserver;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Eloquent\ProjectRepository;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\Eloquent\TaskRepository;
use App\Repositories\Contracts\TaskLogRepositoryInterface;
use App\Repositories\Eloquent\TaskLogRepository;
use App\Repositories\Contracts\ProjectRequestRepositoryInterface;
use App\Repositories\Eloquent\ProjectRequestRepository;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\Eloquent\ClientRepository;
use App\Repositories\Contracts\KaryawanRepositoryInterface;
use App\Repositories\Eloquent\KaryawanRepository;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\Eloquent\DashboardRepository;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
    $this->app->bind(
      ProjectRepositoryInterface::class,
      ProjectRepository::class
    );

    $this->app->bind(
      TaskRepositoryInterface::class,
      TaskRepository::class
    );

    $this->app->bind(
      TaskLogRepositoryInterface::class,
      TaskLogRepository::class
    );
    
    $this->app->bind(
      ProjectRequestRepositoryInterface::class,
      ProjectRequestRepository::class
    );

    $this->app->bind(
      ClientRepositoryInterface::class,
      ClientRepository::class
    );

    $this->app->bind(
      KaryawanRepositoryInterface::class,
      KaryawanRepository::class
    );

    $this->app->bind(
      DashboardRepositoryInterface::class,
      DashboardRepository::class
    );

    $this->app->bind(
      UserRepositoryInterface::class,
      UserRepository::class
    );

  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    Task::observe(TaskObserver::class);
  }
}
