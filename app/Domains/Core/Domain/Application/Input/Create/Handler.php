<?php

namespace App\Domains\Core\Domain\Application\Input\Create;

use App\Domains\Core\Domain\Application\Budget\Component\Recalculate\Handler as RecalculateHandler;
use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\Contracts\BudgetComponentRepository;
use App\Domains\Core\Domain\Contracts\InputRepository;
use App\Domains\Core\Domain\Input;
use App\Domains\Core\Domain\Services\HierarchyRules;
use App\Domains\Core\Domain\Stage;
use Illuminate\Support\Facades\DB;

class Handler
{
    public function __construct(
        private InputRepository $inputRepository,
        private BudgetComponentRepository $componentRepository,
        private HierarchyRules $rules,
        private RecalculateHandler $recalculateHandler,
    ) {}

    public function handle(Command $command): Input
    {
        $stage = $command->getStage() ?? Stage::query()->findOrFail($command->getParentStageId());
        $budget = $command->getBudget() ?? $this->rules->assertLeafCanBelongToStage($stage)->budget;

        return $this->create($budget, $stage, $command);
    }

    private function create(Budget $budget, Stage $stage, Command $command): Input
    {
        return DB::transaction(function () use ($budget, $stage, $command): Input {
            $input = $this->inputRepository->save(
                (new Input)
                    ->setDescription($command->getDescription())
                    ->setTotal($command->getTotal())
            );

            $this->componentRepository->save(new BudgetComponent([
                'id' => BudgetComponent::idForInput($input),
                'description' => $input->getDescription(),
                'type' => BudgetComponent::TYPE_INPUT,
                'budget_id' => $budget->getId(),
                'input_id' => $input->getId(),
                'parent_stage_id' => $stage->getId(),
                'total' => $input->getTotal(),
            ]));

            $this->recalculateHandler->recalculateTreeFrom($stage);

            return $input->refresh();
        });
    }
}
