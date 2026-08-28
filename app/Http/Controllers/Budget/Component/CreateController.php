<?php

namespace App\Http\Controllers\Budget\Component;

use App\Domains\Core\Domain\Application\Budget\Component\Create\Command;
use App\Domains\Core\Domain\Application\Budget\Component\Create\Handler;
use App\Domains\Core\Domain\BudgetComponent;
use App\Http\Controllers\Controller;
use App\Http\Resources\BudgetComponent as BudgetComponentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreateController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function store(int $budgetId, Request $request): JsonResponse
    {
        $this->validateSupportedFields($request);

        $data = $request->validate([
            'type' => ['required', 'string', Rule::in([
                BudgetComponent::TYPE_STAGE,
                BudgetComponent::TYPE_COMPOSITION,
                BudgetComponent::TYPE_INPUT,
            ])],
            'description' => ['required', 'string', 'max:255'],
            'total' => ['sometimes', 'numeric', 'min:0'],
            'composition_id' => ['required_if:type,'.BudgetComponent::TYPE_COMPOSITION, 'nullable', 'integer', 'exists:compositions,id'],
            'input_id' => ['required_if:type,'.BudgetComponent::TYPE_INPUT, 'nullable', 'integer', 'exists:inputs,id'],
        ]);

        $component = $this->handler->handle(new Command(
            budgetId: $budgetId,
            type: $data['type'],
            description: $data['description'],
            total: isset($data['total']) ? (float) $data['total'] : null,
            compositionId: isset($data['composition_id']) ? (int) $data['composition_id'] : null,
            inputId: isset($data['input_id']) ? (int) $data['input_id'] : null,
        ));

        return (new BudgetComponentResource($component))
            ->response()
            ->setStatusCode(201);
    }

    private function validateSupportedFields(Request $request): void
    {
        $supportedFields = [
            'type',
            'description',
            'total',
            'composition_id',
            'input_id',
        ];

        $unsupportedFields = array_diff(array_keys($request->all()), $supportedFields);

        if ($unsupportedFields === []) {
            return;
        }

        throw ValidationException::withMessages(array_fill_keys($unsupportedFields, 'This field is not supported.'));
    }
}
