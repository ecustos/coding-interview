<?php

namespace Tests\Feature;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\Composition;
use App\Domains\Core\Domain\CompositionBudgetComponent;
use App\Domains\Core\Domain\Input;
use App\Domains\Core\Domain\InputBudgetComponent;
use App\Domains\Core\Domain\StageBudgetComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
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

    public function test_budget_show_update_and_delete_return_404_for_missing_budget(): void
    {
        $this->getJson('/api/budgets/999')
            ->assertNotFound();

        $this->putJson('/api/budgets/999', [
            'description' => 'Reforma Residencial',
        ])->assertNotFound();

        $this->deleteJson('/api/budgets/999')
            ->assertNotFound();
    }

    public function test_budget_component_crud_flow_uses_a_single_route_for_every_type(): void
    {
        $budget = Budget::factory()->create();
        $compositionReference = Composition::factory()->create();
        $updatedCompositionReference = Composition::factory()->create();
        $inputReference = Input::factory()->create();

        $stage = $this->createComponent($budget, BudgetComponent::TYPE_STAGE, 'Servicos preliminares');
        $composition = $this->createComponent($budget, BudgetComponent::TYPE_COMPOSITION, 'Concreto', 200, compositionId: $compositionReference->id);
        $input = $this->createComponent($budget, BudgetComponent::TYPE_INPUT, 'Areia', 50, inputId: $inputReference->id);

        $this->getJson("/api/budget/{$budget->id}/component")
            ->assertOk()
            ->assertJsonCount(3)
            ->assertJsonPath('0.description', 'Servicos preliminares')
            ->assertJsonPath('1.type', BudgetComponent::TYPE_COMPOSITION)
            ->assertJsonPath('2.type', BudgetComponent::TYPE_INPUT);

        $this->getJson("/api/budget/{$budget->id}/component/{$composition['id']}")
            ->assertOk()
            ->assertJsonPath('description', 'Concreto');

        $this->putJson("/api/budget/{$budget->id}/component/{$composition['id']}", [
            'type' => BudgetComponent::TYPE_COMPOSITION,
            'description' => 'Concreto usinado',
            'total' => 300,
            'composition_id' => $updatedCompositionReference->id,
        ])
            ->assertOk()
            ->assertJsonPath('description', 'Concreto usinado')
            ->assertJsonPath('composition_id', $updatedCompositionReference->id)
            ->assertJsonPath('total', '300.00');

        $this->deleteJson("/api/budget/{$budget->id}/component/{$input['id']}")
            ->assertNoContent();

        $this->assertDatabaseHas('budget_components', ['id' => $stage['id']]);
        $this->assertDatabaseMissing('budget_components', ['id' => $input['id']]);
        $this->assertSame('0.00', $budget->refresh()->total);
    }

    public function test_budget_component_records_are_returned_as_concrete_component_classes(): void
    {
        $budget = Budget::factory()->create();
        $compositionReference = Composition::factory()->create();
        $inputReference = Input::factory()->create();
        $stage = StageBudgetComponent::factory()->for($budget)->create(['description' => 'Servicos preliminares']);
        $composition = CompositionBudgetComponent::factory()
            ->for($budget)
            ->for($compositionReference)
            ->create();
        $input = InputBudgetComponent::factory()
            ->for($budget)
            ->for($inputReference)
            ->create();

        $this->assertInstanceOf(StageBudgetComponent::class, BudgetComponent::query()->findOrFail($stage->id));
        $this->assertInstanceOf(CompositionBudgetComponent::class, BudgetComponent::query()->findOrFail($composition->id));
        $this->assertInstanceOf(InputBudgetComponent::class, BudgetComponent::query()->findOrFail($input->id));
    }

    public function test_budget_show_returns_components(): void
    {
        $budget = Budget::factory()->create();
        $inputReference = Input::factory()->create();
        $this->createComponent($budget, BudgetComponent::TYPE_STAGE, 'Servicos preliminares');
        $this->createComponent($budget, BudgetComponent::TYPE_INPUT, 'Madeira', 75, inputId: $inputReference->id);

        $this->getJson("/api/budgets/{$budget->id}")
            ->assertOk()
            ->assertJsonPath('description', $budget->description)
            ->assertJsonPath('components.0.description', 'Servicos preliminares')
            ->assertJsonPath('components.1.description', 'Madeira');
    }

    public function test_budget_component_rejects_unknown_type(): void
    {
        $budget = Budget::factory()->create();

        $this->postJson("/api/budget/{$budget->id}/component", [
            'type' => 'service',
            'description' => 'Servico',
            'total' => 10,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['type']);
    }

    public function test_budget_component_rejects_unsupported_fields(): void
    {
        $budget = Budget::factory()->create();

        $this->postJson("/api/budget/{$budget->id}/component", [
            'type' => BudgetComponent::TYPE_STAGE,
            'description' => 'Servicos preliminares',
            'metadata' => 'extra',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['metadata']);
    }

    #[DataProvider('componentSpecificIdProvider')]
    public function test_budget_component_requires_the_specific_id_for_composition_and_input(string $type, string $field): void
    {
        $budget = Budget::factory()->create();

        $this->postJson("/api/budget/{$budget->id}/component", [
            'type' => $type,
            'description' => 'Servico',
            'total' => 10,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$field]);
    }

    #[DataProvider('componentSpecificIdProvider')]
    public function test_budget_component_rejects_unknown_specific_id_for_composition_and_input(string $type, string $field): void
    {
        $budget = Budget::factory()->create();

        $this->postJson("/api/budget/{$budget->id}/component", [
            'type' => $type,
            'description' => 'Servico',
            'total' => 10,
            $field => 999,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$field]);
    }

    public function test_budget_component_update_rejects_type_mismatch(): void
    {
        $budget = Budget::factory()->create();
        $compositionReference = Composition::factory()->create();
        $inputReference = Input::factory()->create();
        $input = $this->createComponent($budget, BudgetComponent::TYPE_INPUT, 'Tinta', 60, inputId: $inputReference->id);

        $this->putJson("/api/budget/{$budget->id}/component/{$input['id']}", [
            'type' => BudgetComponent::TYPE_COMPOSITION,
            'description' => 'Tinta',
            'total' => 60,
            'composition_id' => $compositionReference->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['type']);
    }

    public function test_budget_component_from_another_budget_returns_404(): void
    {
        $sourceBudget = Budget::factory()->create();
        $targetBudget = Budget::factory()->create();
        $component = $this->createComponent($sourceBudget, BudgetComponent::TYPE_STAGE, 'Servicos preliminares');

        $this->getJson("/api/budget/{$targetBudget->id}/component/{$component['id']}")
            ->assertNotFound();
    }

    public function test_budget_component_show_update_and_delete_return_404_for_missing_component(): void
    {
        $budget = Budget::factory()->create();

        $this->getJson("/api/budget/{$budget->id}/component/999")
            ->assertNotFound();

        $this->putJson("/api/budget/{$budget->id}/component/999", [
            'type' => BudgetComponent::TYPE_STAGE,
            'description' => 'Servicos preliminares',
            'total' => 0,
        ])->assertNotFound();

        $this->deleteJson("/api/budget/{$budget->id}/component/999")
            ->assertNotFound();
    }

    private function createComponent(
        Budget $budget,
        string $type,
        string $description,
        ?float $total = null,
        ?int $compositionId = null,
        ?int $inputId = null,
    ): array {
        $payload = [
            'type' => $type,
            'description' => $description,
        ];

        if ($total !== null) {
            $payload['total'] = $total;
        }

        if ($compositionId !== null) {
            $payload['composition_id'] = $compositionId;
        }

        if ($inputId !== null) {
            $payload['input_id'] = $inputId;
        }

        return $this->postJson("/api/budget/{$budget->id}/component", $payload)
            ->assertCreated()
            ->json();
    }

    public static function componentSpecificIdProvider(): array
    {
        return [
            'composition id' => [BudgetComponent::TYPE_COMPOSITION, 'composition_id'],
            'input id' => [BudgetComponent::TYPE_INPUT, 'input_id'],
        ];
    }
}
