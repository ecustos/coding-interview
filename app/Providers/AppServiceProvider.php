<?php

namespace App\Providers;

use App\Domains\Core\Domain\Contracts\BudgetComponentRepository;
use App\Domains\Core\Domain\Contracts\BudgetRepository;
use App\Domains\Core\Domain\Infra\Eloquent\BudgetComponentRepository as EloquentBudgetComponentRepository;
use App\Domains\Core\Domain\Infra\Eloquent\BudgetRepository as EloquentBudgetRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BudgetRepository::class, EloquentBudgetRepository::class);
        $this->app->bind(BudgetComponentRepository::class, EloquentBudgetComponentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
}
