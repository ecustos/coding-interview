<?php

namespace App\Domains\Core\Domain\Application\Budget\Delete;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\Contracts\BudgetRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class Handler
{
    public function __construct(private BudgetRepository $budgetRepository) {}

    public function handle(Command $command): void
    {
        DB::transaction(function () use ($command): void {
            $budget = $this->budgetRepository->find($command->getBudgetId());

            if (! $budget) {
                throw (new ModelNotFoundException)->setModel(Budget::class, [$command->getBudgetId()]);
            }

            $this->budgetRepository->destroy($budget);
        });
    }
}
