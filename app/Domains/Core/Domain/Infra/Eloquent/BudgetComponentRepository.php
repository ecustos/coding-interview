<?php

namespace App\Domains\Core\Domain\Infra\Eloquent;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\Contracts\BudgetComponentRepository as BudgetComponentRepositoryContract;
use Illuminate\Support\Collection;

class BudgetComponentRepository implements BudgetComponentRepositoryContract
{
    public function save(BudgetComponent $component): BudgetComponent
    {
        $component->save();

        return $component->refresh();
    }

    public function destroy(BudgetComponent $component): bool
    {
        return (bool) $component->delete();
    }

    public function getByBudget(Budget $budget): Collection
    {
        return BudgetComponent::query()
            ->where('budget_id', $budget->getId())
            ->orderBy('id')
            ->get();
    }

    public function findForBudget(Budget $budget, int $componentId): ?BudgetComponent
    {
        return BudgetComponent::query()
            ->where('budget_id', $budget->getId())
            ->where('id', $componentId)
            ->first();
    }
}
