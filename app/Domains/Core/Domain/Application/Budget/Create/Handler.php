<?php

namespace App\Domains\Core\Domain\Application\Budget\Create;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\Contracts\BudgetRepository;

class Handler
{
    public function __construct(private BudgetRepository $budgetRepository) {}

    public function handle(Command $command): Budget
    {
        $budget = (new Budget)
            ->setDescription($command->getDescription())
            ->setTotal(0);

        return $this->budgetRepository->save($budget);
    }
}
