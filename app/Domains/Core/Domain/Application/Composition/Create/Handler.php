<?php

namespace App\Domains\Core\Domain\Application\Composition\Create;

use App\Domains\Core\Domain\Application\Budget\Component\Recalculate\Handler as RecalculateHandler;
use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\Composition;
use App\Domains\Core\Domain\Contracts\BudgetComponentRepository;
use App\Domains\Core\Domain\Contracts\CompositionRepository;
use App\Domains\Core\Domain\Services\HierarchyRules;
use App\Domains\Core\Domain\Stage;
use Illuminate\Support\Facades\DB;

class Handler
{
    public function __construct(
        private CompositionRepository $compositionRepository,
        private BudgetComponentRepository $componentRepository,
        private HierarchyRules $rules,
        private RecalculateHandler $recalculateHandler,
    ) {}

    public function handle(Command $command): Composition
    {
        $stage = $command->getStage() ?? Stage::query()->findOrFail($command->getParentStageId());
        $budget = $command->getBudget() ?? $this->rules->assertLeafCanBelongToStage($stage)->budget;

        return $this->create($budget, $stage, $command);
    }

    private function create(Budget $budget, Stage $stage, Command $command): Composition
    {
        return DB::transaction(function () use ($budget, $stage, $command): Composition {
            $composition = $this->compositionRepository->save(
                (new Composition)
                    ->setDescription($command->getDescription())
                    ->setTotal($command->getTotal())
            );

            $this->componentRepository->save(new BudgetComponent([
                'id' => BudgetComponent::idForComposition($composition),
                'description' => $composition->getDescription(),
                'type' => BudgetComponent::TYPE_COMPOSITION,
                'budget_id' => $budget->getId(),
                'composition_id' => $composition->getId(),
                'parent_stage_id' => $stage->getId(),
                'total' => $composition->getTotal(),
            ]));

            $this->recalculateHandler->recalculateTreeFrom($stage);

            return $composition->refresh();
        });
    }
}
