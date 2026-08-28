<?php

namespace App\Domains\Core\Domain\Application\Budget\Component\Create;

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

    public function handle(Command $command): BudgetComponent
    {
        return DB::transaction(function () use ($command): BudgetComponent {
            $budget = $this->budgetRepository->find($command->getBudgetId());

            if (! $budget) {
                throw (new ModelNotFoundException)->setModel(Budget::class, [$command->getBudgetId()]);
            }

            $strategy = Factory::create($command->getType());
            $component = $strategy->setFields($command, $strategy->createComponent());

            return $this->componentRepository->save($component);
        });
    }
}
