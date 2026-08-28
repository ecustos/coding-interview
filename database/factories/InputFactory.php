<?php

namespace Database\Factories;

use App\Domains\Core\Domain\Input;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Input> */
class InputFactory extends Factory
{
    protected $model = Input::class;

    public function definition(): array
    {
        return [
            'description' => fake()->words(2, true),
            'unit_price' => fake()->randomFloat(2, 10, 300),
        ];
    }
}
