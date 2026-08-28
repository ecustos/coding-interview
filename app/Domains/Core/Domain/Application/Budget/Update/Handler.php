<?php

namespace App\Domains\Core\Domain\Application\Budget\Update;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\Contracts\BudgetRepository;

class Handler
{
    public function __construct(private BudgetRepository $budgetRepository) {}

    public function handle(Command $command): Budget
    {
        $budget = $command->getBudget()->setDescription($command->getDescription());

        return $this->budgetRepository->save($budget);
    }
}
