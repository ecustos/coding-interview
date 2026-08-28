<?php

namespace App\Domains\Core\Domain\Application\Input\Update;

use App\Domains\Core\Domain\Application\Budget\Component\Recalculate\Handler as RecalculateHandler;
use App\Domains\Core\Domain\Contracts\BudgetComponentRepository;
use App\Domains\Core\Domain\Contracts\InputRepository;
use App\Domains\Core\Domain\Input;
use Illuminate\Support\Facades\DB;

class Handler
{
    public function __construct(
        private InputRepository $inputRepository,
        private BudgetComponentRepository $componentRepository,
        private RecalculateHandler $recalculateHandler,
    ) {}

    public function handle(Command $command): Input
    {
        return DB::transaction(function () use ($command): Input {
            $input = $this->inputRepository->save(
                $command->getInput()
                    ->setDescription($command->getDescription())
                    ->setTotal($command->getTotal())
            );

            $component = $input->component()->firstOrFail();
            $component->forceFill([
                'description' => $input->getDescription(),
                'total' => $input->getTotal(),
            ]);

            $this->componentRepository->save($component);
            $this->recalculateHandler->recalculateDirectStageAndBudget($component->parentStage);

            return $input->refresh();
        });
    }
}
