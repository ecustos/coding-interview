<?php

namespace App\Providers;

use App\Domains\Core\Domain\Contracts\BudgetComponentRepository;
use App\Domains\Core\Domain\Contracts\BudgetRepository;
use App\Domains\Core\Domain\Contracts\CompositionRepository;
use App\Domains\Core\Domain\Contracts\InputRepository;
use App\Domains\Core\Domain\Contracts\StageRepository;
use App\Domains\Core\Domain\Infra\Eloquent\BudgetComponentRepository as EloquentBudgetComponentRepository;
use App\Domains\Core\Domain\Infra\Eloquent\BudgetRepository as EloquentBudgetRepository;
use App\Domains\Core\Domain\Infra\Eloquent\CompositionRepository as EloquentCompositionRepository;
use App\Domains\Core\Domain\Infra\Eloquent\InputRepository as EloquentInputRepository;
use App\Domains\Core\Domain\Infra\Eloquent\StageRepository as EloquentStageRepository;
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
        $this->app->bind(StageRepository::class, EloquentStageRepository::class);
        $this->app->bind(CompositionRepository::class, EloquentCompositionRepository::class);
        $this->app->bind(InputRepository::class, EloquentInputRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
}
