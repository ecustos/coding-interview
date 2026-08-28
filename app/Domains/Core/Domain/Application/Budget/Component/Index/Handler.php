<?php

namespace App\Domains\Core\Domain\Application\Budget\Component\Index;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\Contracts\BudgetComponentRepository;
use App\Domains\Core\Domain\Contracts\BudgetRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class Handler
{
    public function __construct(
        private BudgetComponentRepository $componentRepository,
        private BudgetRepository $budgetRepository,
    ) {}

    public function handle(Command $command): Collection
    {
        $budget = $this->budgetRepository->find($command->getBudgetId());

        if (! $budget) {
            throw (new ModelNotFoundException)->setModel(Budget::class, [$command->getBudgetId()]);
        }

        return $this->componentRepository->getByBudget($budget);
    }
}
