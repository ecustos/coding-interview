<?php

namespace Database\Factories;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BudgetComponent> */
class BudgetComponentFactory extends Factory
{
    protected $model = BudgetComponent::class;

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
