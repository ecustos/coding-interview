<?php

namespace App\Domains\Core\Domain\Application\Budget\Component\Update;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\CompositionBudgetComponent;
use App\Domains\Core\Domain\Contracts\BudgetComponentRepository;
use App\Domains\Core\Domain\Contracts\BudgetRepository;
use App\Domains\Core\Domain\InputBudgetComponent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

            $component = $this->componentRepository->findForBudget(
                $budget,
                $command->getComponentId(),
            );

            if (! $component) {
                throw (new ModelNotFoundException)->setModel(BudgetComponent::class, [$command->getComponentId()]);
            }

            if ($component->getType() !== $command->getType()) {
                throw ValidationException::withMessages([
                    'type' => 'The selected type is invalid for this component.',
                ]);
            }

            $total = $command->getTotal() ?? 0;

            $component->forceFill([
                'description' => $command->getDescription(),
                'total' => $total,
            ]);

            if ($component instanceof CompositionBudgetComponent) {
                $component->setCompositionId($command->getCompositionId());
            }

            if ($component instanceof InputBudgetComponent) {
                $component->setInputId($command->getInputId());
            }

            return $this->componentRepository->save($component);
        });
    }
}
