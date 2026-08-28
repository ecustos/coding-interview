<?php

namespace App\Domains\Core\Domain\Contracts;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use Illuminate\Support\Collection;

interface BudgetComponentRepository
{
    public function save(BudgetComponent $component): BudgetComponent;

    public function destroy(BudgetComponent $component): bool;

    public function getStageChildrenTotal(int $stageId, string $type): float;

    public function getRootStages(Budget $budget): Collection;

    public function getChildren(BudgetComponent $component): Collection;
}
