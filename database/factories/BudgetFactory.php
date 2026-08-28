<?php

namespace Database\Factories;

use App\Domains\Core\Domain\Budget;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Budget> */
class BudgetFactory extends Factory
{
    protected $model = Budget::class;

    public function definition(): array
    {
        return [
            'description' => fake()->sentence(3),
            'total' => 0,
        ];
    }
}
