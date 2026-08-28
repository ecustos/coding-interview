<?php

namespace App\Domains\Core\Domain\Contracts;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use Illuminate\Support\Collection;

interface BudgetComponentRepository
{
    public function save(BudgetComponent $component): BudgetComponent;

    public function destroy(BudgetComponent $component): bool;

    public function getByBudget(Budget $budget): Collection;

    public function findForBudget(Budget $budget, int $componentId): ?BudgetComponent;
}
