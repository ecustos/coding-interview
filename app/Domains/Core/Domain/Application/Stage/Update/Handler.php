<?php

namespace App\Domains\Core\Domain\Application\Stage\Update;

use App\Domains\Core\Domain\Application\Budget\Component\Recalculate\Handler as RecalculateHandler;
use App\Domains\Core\Domain\Contracts\StageRepository;
use App\Domains\Core\Domain\Stage;
use Illuminate\Support\Facades\DB;

class Handler
{
    public function __construct(
        private StageRepository $stageRepository,
        private RecalculateHandler $recalculateHandler,
    ) {}

    public function handle(Command $command): Stage
    {
        return DB::transaction(function () use ($command): Stage {
            $stage = $this->stageRepository->save(
                $command->getStage()->setDescription($command->getDescription())
            );

            $stage->component()->update(['description' => $stage->getDescription()]);
            $this->recalculateHandler->recalculateTreeFrom($stage);

            return $stage->refresh();
        });
    }
}
