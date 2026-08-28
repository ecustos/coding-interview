<?php

namespace App\Domains\Core\Domain\Application\Budget\Update;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\Contracts\BudgetRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Handler
{
    public function __construct(private BudgetRepository $budgetRepository) {}

    public function handle(Command $command): Budget
    {
        $budget = $this->budgetRepository->find($command->getBudgetId());

        if (! $budget) {
            throw (new ModelNotFoundException)->setModel(Budget::class, [$command->getBudgetId()]);
        }

        $budget->setDescription($command->getDescription());

        return $this->budgetRepository->save($budget);
    }
}
