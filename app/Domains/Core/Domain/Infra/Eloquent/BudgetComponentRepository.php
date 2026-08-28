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

    public function getStageChildrenTotal(int $stageId, string $type): float
    {
        return (float) BudgetComponent::query()
            ->where('type', $type)
            ->where('parent_stage_id', $stageId)
            ->sum('total');
    }

    public function getRootStages(Budget $budget): Collection
    {
        return BudgetComponent::query()
            ->where('budget_id', $budget->getId())
            ->where('type', BudgetComponent::TYPE_STAGE)
            ->whereNull('parent_stage_id')
            ->orderBy('id')
            ->get();
    }

    public function getChildren(BudgetComponent $component): Collection
    {
        return BudgetComponent::query()
            ->where('parent_stage_id', $component->getId())
            ->orderBy('id')
            ->get();
    }
}
