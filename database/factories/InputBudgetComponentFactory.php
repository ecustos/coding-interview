<?php

namespace Database\Factories;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\Input;
use App\Domains\Core\Domain\InputBudgetComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<InputBudgetComponent> */
class InputBudgetComponentFactory extends Factory
{
    protected $model = InputBudgetComponent::class;

    public function definition(): array
    {
        return [
            'description' => fake()->words(2, true),
            'type' => BudgetComponent::TYPE_INPUT,
            'budget_id' => Budget::factory(),
            'input_id' => Input::factory(),
            'total' => fake()->randomFloat(2, 10, 300),
        ];
    }
}
