<?php

namespace App\Domains\Core\Domain\Infra\Eloquent;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\Contracts\StageRepository as StageRepositoryContract;
use App\Domains\Core\Domain\Stage;
use Illuminate\Support\Collection;

class StageRepository implements StageRepositoryContract
{
    public function save(Stage $stage): Stage
    {
        $stage->save();

        return $stage->refresh();
    }

    public function destroy(Stage $stage): bool
    {
        return (bool) $stage->delete();
    }

    public function getByBudget(Budget $budget): Collection
    {
        $ids = $budget->components()
            ->where('type', BudgetComponent::TYPE_STAGE)
            ->orderBy('id')
            ->pluck('id');

        return Stage::query()->whereIn('id', $ids)->orderBy('id')->get();
    }
}
