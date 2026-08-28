<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetComponent extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getId(),
            'description' => $this->getDescription(),
            'type' => $this->getType(),
            'budget_id' => $this->budget_id,
            'composition_id' => $this->composition_id,
            'input_id' => $this->input_id,
            'total' => number_format($this->getTotal(), 2, '.', ''),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
