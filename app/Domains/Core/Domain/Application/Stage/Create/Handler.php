<?php

namespace App\Domains\Core\Domain\Application\Stage\Create;

use App\Domains\Core\Domain\Application\Budget\Component\Recalculate\Handler as RecalculateHandler;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\Contracts\BudgetComponentRepository;
use App\Domains\Core\Domain\Contracts\StageRepository;
use App\Domains\Core\Domain\Services\HierarchyRules;
use App\Domains\Core\Domain\Stage;
use Illuminate\Support\Facades\DB;

class Handler
{
    public function __construct(
        private StageRepository $stageRepository,
        private BudgetComponentRepository $componentRepository,
        private HierarchyRules $rules,
        private RecalculateHandler $recalculateHandler,
    ) {}

    public function handle(Command $command): Stage
    {
        return DB::transaction(function () use ($command): Stage {
            $parentStage = $this->rules->assertStageCanBelongToBudget(
                $command->getBudget(),
                $command->getParentStageId(),
            );

            $stage = $this->stageRepository->save(
                (new Stage)->setDescription($command->getDescription())->setTotal(0)
            );

            $this->componentRepository->save(new BudgetComponent([
                'id' => BudgetComponent::idForStage($stage),
                'description' => $stage->getDescription(),
                'type' => BudgetComponent::TYPE_STAGE,
                'budget_id' => $command->getBudget()->getId(),
                'parent_stage_id' => $parentStage?->getId(),
                'total' => 0,
            ]));

            if ($parentStage) {
                $this->recalculateHandler->recalculateTreeFrom($parentStage);
            } else {
                $this->recalculateHandler->recalculateBudget($command->getBudget());
            }

            return $stage->refresh();
        });
    }
}
