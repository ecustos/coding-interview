<?php

namespace App\Domains\Core\Domain\Application\Budget\Delete;

use App\Domains\Core\Domain\Contracts\BudgetRepository;
use App\Domains\Core\Domain\Stage;
use Illuminate\Support\Facades\DB;

class Handler
{
    public function __construct(private BudgetRepository $budgetRepository) {}

    public function handle(Command $command): void
    {
        DB::transaction(function () use ($command): void {
            $budget = $command->getBudget();

            $budget->components()
                ->whereNotNull('composition_id')
                ->with('composition')
                ->get()
                ->each(fn ($component) => $component->composition?->delete());

            $budget->components()
                ->whereNotNull('input_id')
                ->with('input')
                ->get()
                ->each(fn ($component) => $component->input?->delete());

            $stageIds = $budget->components()
                ->where('type', 'stage')
                ->pluck('id');

            $this->budgetRepository->destroy($budget);
            Stage::query()->whereIn('id', $stageIds)->delete();
        });
    }
}
