<?php

namespace App\Domains\Core\Domain;

use Database\Factories\InputBudgetComponentFactory;

class InputBudgetComponent extends BudgetComponent
{
    protected static ?string $componentType = self::TYPE_INPUT;

    protected $attributes = [
        'type' => self::TYPE_INPUT,
        'total' => 0,
    ];

    protected static function newFactory(): InputBudgetComponentFactory
    {
        return InputBudgetComponentFactory::new();
    }

    public function getInputId(): ?int
    {
        return $this->input_id;
    }

    public function setInputId(?int $inputId): self
    {
        $this->input_id = $inputId;

        return $this;
    }
}
