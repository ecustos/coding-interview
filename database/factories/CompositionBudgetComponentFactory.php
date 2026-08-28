<?php

namespace Database\Factories;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\Composition;
use App\Domains\Core\Domain\CompositionBudgetComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CompositionBudgetComponent> */
class CompositionBudgetComponentFactory extends Factory
{
    protected $model = CompositionBudgetComponent::class;

    public function definition(): array
    {
        return [
            'description' => fake()->words(3, true),
            'type' => BudgetComponent::TYPE_COMPOSITION,
            'budget_id' => Budget::factory(),
            'composition_id' => Composition::factory(),
            'total' => fake()->randomFloat(2, 50, 500),
        ];
    }
}
