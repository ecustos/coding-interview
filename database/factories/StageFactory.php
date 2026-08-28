<?php

namespace Database\Factories;

use App\Domains\Core\Domain\Stage;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Stage> */
class StageFactory extends Factory
{
    protected $model = Stage::class;

    public function definition(): array
    {
        return [
            'description' => fake()->words(3, true),
            'total' => 0,
        ];
    }
}
