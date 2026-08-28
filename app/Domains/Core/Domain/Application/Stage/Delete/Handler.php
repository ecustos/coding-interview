<?php

namespace App\Domains\Core\Domain\Application\Stage\Delete;

use App\Domains\Core\Domain\Application\Budget\Component\Recalculate\Handler as RecalculateHandler;
use App\Domains\Core\Domain\Contracts\BudgetComponentRepository;
use App\Domains\Core\Domain\Contracts\StageRepository;
use App\Domains\Core\Domain\Stage;
use Illuminate\Support\Facades\DB;

class Handler
{
    public function __construct(
        private StageRepository $stageRepository,
        private BudgetComponentRepository $componentRepository,
        private RecalculateHandler $recalculateHandler,
    ) {}

    public function handle(Command $command): void
    {
        DB::transaction(function () use ($command): void {
            $stage = $command->getStage();
            $component = $stage->component()->firstOrFail();
            $parentStage = $component->getParentStageId() ? Stage::query()->find($component->getParentStageId()) : null;
            $budget = $component->budget;

            $this->componentRepository->destroy($component);
            $this->stageRepository->destroy($stage);

            if ($parentStage) {
                $this->recalculateHandler->recalculateTreeFrom($parentStage);

                return;
            }

            $this->recalculateHandler->recalculateBudget($budget);
        });
    }
}
