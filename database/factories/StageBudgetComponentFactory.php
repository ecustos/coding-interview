<?php

namespace Database\Factories;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\StageBudgetComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<StageBudgetComponent> */
class StageBudgetComponentFactory extends Factory
{
    protected $model = StageBudgetComponent::class;

    public function definition(): array
    {
        return [
            'description' => fake()->words(3, true),
            'type' => BudgetComponent::TYPE_STAGE,
            'budget_id' => Budget::factory(),
            'total' => 0,
        ];
    }
}
