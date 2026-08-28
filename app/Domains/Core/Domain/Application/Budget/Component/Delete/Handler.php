<?php

namespace App\Domains\Core\Domain\Application\Budget\Component\Delete;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\Contracts\BudgetComponentRepository;
use App\Domains\Core\Domain\Contracts\BudgetRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class Handler
{
    public function __construct(
        private BudgetComponentRepository $componentRepository,
        private BudgetRepository $budgetRepository,
    ) {}

    public function handle(Command $command): void
    {
        DB::transaction(function () use ($command): void {
            $budget = $this->budgetRepository->find($command->getBudgetId());

            if (! $budget) {
                throw (new ModelNotFoundException)->setModel(Budget::class, [$command->getBudgetId()]);
            }

            $component = $this->componentRepository->findForBudget(
                $budget,
                $command->getComponentId(),
            );

            if (! $component) {
                throw (new ModelNotFoundException)->setModel(BudgetComponent::class, [$command->getComponentId()]);
            }

            $this->componentRepository->destroy($component);
        });
    }
}
