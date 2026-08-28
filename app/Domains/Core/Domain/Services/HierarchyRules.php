<?php

namespace App\Domains\Core\Domain\Services;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\Stage;
use Illuminate\Validation\ValidationException;

class HierarchyRules
{
    public function assertStageCanBelongToBudget(Budget $budget, ?int $parentStageId): ?Stage
    {
        if ($parentStageId === null) {
            return null;
        }

        $parentStage = Stage::query()->find($parentStageId);

        if (! $parentStage) {
            throw ValidationException::withMessages([
                'parent_stage_id' => 'The selected parent stage is invalid.',
            ]);
        }

        return $parentStage;
    }

    public function assertLeafCanBelongToStage(Stage $stage): BudgetComponent
    {
        $component = $stage->component()->first();

        if (! $component || $component->getType() !== BudgetComponent::TYPE_STAGE) {
            throw ValidationException::withMessages([
                'parent_stage_id' => 'The selected parent stage is invalid.',
            ]);
        }

        return $component;
    }
}
