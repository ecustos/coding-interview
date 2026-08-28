<?php

namespace Database\Seeders;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MiniEcustosSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $now = now();
        $budget = Budget::query()->create([
            'description' => 'Reforma',
            'total' => 0,
        ]);

        BudgetComponent::query()->insert([
            [
                'description' => 'Servicos preliminares',
                'type' => BudgetComponent::TYPE_STAGE,
                'budget_id' => $budget->getId(),
                'composition_id' => null,
                'input_id' => null,
                'total' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'description' => 'Fundacao',
                'type' => BudgetComponent::TYPE_STAGE,
                'budget_id' => $budget->getId(),
                'composition_id' => null,
                'input_id' => null,
                'total' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'description' => 'Areia fina',
                'type' => BudgetComponent::TYPE_INPUT,
                'budget_id' => $budget->getId(),
                'composition_id' => null,
                'input_id' => 1,
                'total' => 120,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'description' => 'Cimento top',
                'type' => BudgetComponent::TYPE_COMPOSITION,
                'budget_id' => $budget->getId(),
                'composition_id' => 1,
                'input_id' => null,
                'total' => 300,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'description' => 'Acabamento',
                'type' => BudgetComponent::TYPE_STAGE,
                'budget_id' => $budget->getId(),
                'composition_id' => null,
                'input_id' => null,
                'total' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'description' => 'Betorneia',
                'type' => BudgetComponent::TYPE_INPUT,
                'budget_id' => $budget->getId(),
                'composition_id' => null,
                'input_id' => 2,
                'total' => 80,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
