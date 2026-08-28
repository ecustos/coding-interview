<?php

namespace App\Domains\Core\Domain\Application\Budget\Show;

use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\Contracts\BudgetComponentRepository;

class Handler
{
    public function __construct(private BudgetComponentRepository $componentRepository) {}

    public function handle(Command $command): array
    {
        $budget = $command->getBudget();

        return [
            'id' => $budget->getId(),
            'description' => $budget->getDescription(),
            'total' => $budget->getTotal(),
            'components' => $this->componentRepository
                ->getRootStages($budget)
                ->map(fn (BudgetComponent $component): array => $this->node($component))
                ->values()
                ->all(),
        ];
    }

    private function node(BudgetComponent $component): array
    {
        return [
            'id' => $component->getId(),
            'description' => $component->description,
            'type' => $component->getType(),
            'total' => (float) $component->total,
            'parent_stage_id' => $component->getParentStageId(),
            'children' => $this->componentRepository
                ->getChildren($component)
                ->map(fn (BudgetComponent $child): array => $this->node($child))
                ->values()
                ->all(),
        ];
    }
}
