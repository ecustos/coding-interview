<?php

namespace App\Domains\Core\Domain\Application\Budget\Component\Recalculate;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\Contracts\BudgetComponentRepository;
use App\Domains\Core\Domain\Contracts\BudgetRepository;
use App\Domains\Core\Domain\Contracts\StageRepository;
use App\Domains\Core\Domain\Stage;

class Handler
{
    public function __construct(
        private BudgetRepository $budgetRepository,
        private StageRepository $stageRepository,
        private BudgetComponentRepository $componentRepository,
    ) {}

    public function recalculateBudget(Budget $budget): Budget
    {
        $budget->setTotal($this->budgetRepository->getRootStageTotal($budget));

        return $this->budgetRepository->save($budget);
    }

    public function recalculateTreeFrom(Stage $stage): void
    {
        $component = $stage->component()->firstOrFail();

        $this->recalculateStage($stage);

        while ($component->getParentStageId() !== null) {
            $parent = Stage::query()->findOrFail($component->getParentStageId());
            $this->recalculateStage($parent);
            $component = $parent->component()->firstOrFail();
        }

        $this->recalculateBudget($component->budget);
    }

    public function recalculateStage(Stage $stage): Stage
    {
        $component = $stage->component()->firstOrFail();

        $total = $this->componentRepository->getStageChildrenTotal($stage->getId(), BudgetComponent::TYPE_STAGE)
            + $this->componentRepository->getStageChildrenTotal($stage->getId(), BudgetComponent::TYPE_COMPOSITION)
            + $this->componentRepository->getStageChildrenTotal($stage->getId(), BudgetComponent::TYPE_INPUT);

        $stage->setTotal($total);
        $component->forceFill([
            'description' => $stage->getDescription(),
            'total' => $total,
        ]);

        $this->stageRepository->save($stage);
        $this->componentRepository->save($component);

        return $stage->refresh();
    }

    public function recalculateDirectStageAndBudget(Stage $stage): void
    {
        $stage = $this->recalculateStage($stage);
        $component = $stage->component()->firstOrFail();

        $this->recalculateBudget($component->budget);
    }
}
