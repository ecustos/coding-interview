<?php

namespace Database\Factories;

use App\Domains\Core\Domain\Composition;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Composition> */
class CompositionFactory extends Factory
{
    protected $model = Composition::class;

    public function definition(): array
    {
        return [
            'description' => fake()->words(3, true),
            'total' => fake()->randomFloat(2, 50, 500),
        ];
    }
}
