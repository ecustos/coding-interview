<?php

namespace Tests\Feature;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiniEcustosSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_domain_data_and_keeps_budget_total(): void
    {
        $this->seed();

        $budget = Budget::query()->where('description', 'Reforma')->firstOrFail();

        $this->assertSame('0.00', $budget->total);

        $this->assertDatabaseHas('inputs', [
            'id' => 1,
            'description' => 'Areia fina',
            'unit_price' => 15,
        ]);
        $this->assertDatabaseHas('compositions', [
            'id' => 1,
            'description' => 'Cimento top',
            'total' => 100,
        ]);
        $this->assertDatabaseHas('budget_components', [
            'budget_id' => $budget->id,
            'type' => BudgetComponent::TYPE_INPUT,
            'input_id' => 1,
            'total' => 120,
        ]);
        $this->assertDatabaseHas('budget_components', [
            'budget_id' => $budget->id,
            'type' => BudgetComponent::TYPE_COMPOSITION,
            'composition_id' => 1,
            'total' => 300,
        ]);

        $this->assertCount(6, BudgetComponent::query()->where('budget_id', $budget->id)->get());
    }
}
