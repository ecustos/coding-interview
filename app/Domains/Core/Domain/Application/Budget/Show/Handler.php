<?php

namespace App\Domains\Core\Domain\Application\Budget\Show;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\Contracts\BudgetRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Handler
{
    public function __construct(private BudgetRepository $budgetRepository) {}

    public function handle(Command $command): array
    {
        $budget = $this->budgetRepository->find($command->getBudgetId());

        if (! $budget) {
            throw (new ModelNotFoundException)->setModel(Budget::class, [$command->getBudgetId()]);
        }

        return [
            'id' => $budget->getId(),
            'description' => $budget->getDescription(),
            'total' => $budget->getTotal(),
            'components' => $this->budgetRepository->components($budget)->values()->all(),
        ];
    }
}
