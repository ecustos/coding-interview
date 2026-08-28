<?php

namespace App\Domains\Core\Domain\Application\Composition\Update;

use App\Domains\Core\Domain\Application\Budget\Component\Recalculate\Handler as RecalculateHandler;
use App\Domains\Core\Domain\Composition;
use App\Domains\Core\Domain\Contracts\BudgetComponentRepository;
use App\Domains\Core\Domain\Contracts\CompositionRepository;
use Illuminate\Support\Facades\DB;

class Handler
{
    public function __construct(
        private CompositionRepository $compositionRepository,
        private BudgetComponentRepository $componentRepository,
        private RecalculateHandler $recalculateHandler,
    ) {}

    public function handle(Command $command): Composition
    {
        return DB::transaction(function () use ($command): Composition {
            $composition = $this->compositionRepository->save(
                $command->getComposition()
                    ->setDescription($command->getDescription())
                    ->setTotal($command->getTotal())
            );

            $component = $composition->component()->firstOrFail();
            $component->forceFill([
                'description' => $composition->getDescription(),
                'total' => $composition->getTotal(),
            ]);

            $this->componentRepository->save($component);
            $this->recalculateHandler->recalculateTreeFrom($component->parentStage);

            return $composition->refresh();
        });
    }
}
