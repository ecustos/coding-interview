<?php

namespace Tests\Feature;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\Stage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiniEcustosApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_budget_crud_flow(): void
    {
        $created = $this->postJson('/api/budgets', [
            'description' => 'Reforma Comercial',
        ]);

        $created->assertCreated()
            ->assertJsonPath('description', 'Reforma Comercial')
            ->assertJsonPath('total', '0.00');

        $budgetId = $created->json('id');

        $this->getJson('/api/budgets')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->putJson("/api/budgets/{$budgetId}", [
            'description' => 'Reforma Residencial',
        ])->assertOk()
            ->assertJsonPath('description', 'Reforma Residencial');

        $this->deleteJson("/api/budgets/{$budgetId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('budgets', ['id' => $budgetId]);
    }

    public function test_creates_stage_substage_composition_and_input_with_totals(): void
    {
        $budget = Budget::factory()->create();

        $stage = $this->postJson("/api/budgets/{$budget->id}/stages", [
            'description' => 'Stage 1',
        ])->assertCreated()->json();

        $subStage = $this->postJson("/api/budgets/{$budget->id}/stages", [
            'description' => 'Stage 1.1',
            'parent_stage_id' => $stage['id'],
        ])->assertCreated()->json();

        $this->postJson("/api/stages/{$subStage['id']}/inputs", [
            'description' => 'Cimento',
            'total' => 100,
        ])->assertCreated();

        $this->postJson("/api/stages/{$stage['id']}/compositions", [
            'description' => 'Concreto',
            'total' => 200,
        ])->assertCreated();

        $this->postJson("/api/stages/{$stage['id']}/inputs", [
            'description' => 'Areia',
            'total' => 50,
        ])->assertCreated();

        $this->assertSame('100.00', Stage::query()->find($subStage['id'])->total);
        $this->assertSame('350.00', Stage::query()->find($stage['id'])->total);
        $this->assertSame('350.00', $budget->refresh()->total);
    }

    public function test_updates_entities_and_recalculates_root_stage_total(): void
    {
        $budget = Budget::factory()->create();
        $stage = $this->postJson("/api/budgets/{$budget->id}/stages", [
            'description' => 'Stage 1',
        ])->json();

        $input = $this->postJson("/api/stages/{$stage['id']}/inputs", [
            'description' => 'Brita',
            'total' => 30,
        ])->json();

        $this->putJson("/api/inputs/{$input['id']}", [
            'description' => 'Brita lavada',
            'total' => 90,
        ])->assertOk()
            ->assertJsonPath('description', 'Brita lavada')
            ->assertJsonPath('total', '90.00');

        $this->assertSame('90.00', Stage::query()->find($stage['id'])->total);
        $this->assertSame('90.00', $budget->refresh()->total);
    }

    public function test_lists_stage_compositions_and_inputs(): void
    {
        $budget = Budget::factory()->create();
        $stage = $this->postJson("/api/budgets/{$budget->id}/stages", [
            'description' => 'Stage 1',
        ])->json();

        $this->postJson("/api/stages/{$stage['id']}/compositions", [
            'description' => 'Alvenaria',
            'total' => 180,
        ]);

        $this->postJson("/api/stages/{$stage['id']}/inputs", [
            'description' => 'Bloco',
            'total' => 70,
        ]);

        $this->getJson("/api/stages/{$stage['id']}/compositions")
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.description', 'Alvenaria');

        $this->getJson("/api/stages/{$stage['id']}/inputs")
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.description', 'Bloco');
    }

    public function test_composition_and_input_need_a_stage_context(): void
    {
        $budget = Budget::factory()->create();

        $this->postJson("/api/budgets/{$budget->id}/compositions", [
            'description' => 'Sem etapa',
            'total' => 10,
        ])->assertUnprocessable();

        $this->postJson("/api/budgets/{$budget->id}/inputs", [
            'description' => 'Sem etapa',
            'total' => 10,
        ])->assertUnprocessable();
    }

    public function test_deletes_leaf_entities_and_their_components(): void
    {
        $budget = Budget::factory()->create();
        $stage = $this->postJson("/api/budgets/{$budget->id}/stages", [
            'description' => 'Stage 1',
        ])->json();

        $composition = $this->postJson("/api/stages/{$stage['id']}/compositions", [
            'description' => 'Pintura',
            'total' => 100,
        ])->json();

        $input = $this->postJson("/api/stages/{$stage['id']}/inputs", [
            'description' => 'Tinta',
            'total' => 60,
        ])->json();

        $this->deleteJson("/api/compositions/{$composition['id']}")->assertNoContent();
        $this->deleteJson("/api/inputs/{$input['id']}")->assertNoContent();

        $this->assertDatabaseMissing('compositions', ['id' => $composition['id']]);
        $this->assertDatabaseMissing('inputs', ['id' => $input['id']]);
        $this->assertDatabaseMissing('budget_components', [
            'id' => BudgetComponent::COMPOSITION_OFFSET + $composition['id'],
        ]);
        $this->assertDatabaseMissing('budget_components', [
            'id' => BudgetComponent::INPUT_OFFSET + $input['id'],
        ]);
    }

    public function test_budget_show_returns_component_structure(): void
    {
        $budget = Budget::factory()->create();
        $stage = $this->postJson("/api/budgets/{$budget->id}/stages", [
            'description' => 'Stage 1',
        ])->json();

        $this->postJson("/api/stages/{$stage['id']}/inputs", [
            'description' => 'Madeira',
            'total' => 75,
        ]);

        $this->getJson("/api/budgets/{$budget->id}")
            ->assertOk()
            ->assertJsonPath('description', $budget->description)
            ->assertJsonPath('components.0.description', 'Stage 1')
            ->assertJsonPath('components.0.children.0.description', 'Madeira');
    }
}
