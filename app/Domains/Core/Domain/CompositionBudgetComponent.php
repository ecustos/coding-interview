<?php

namespace App\Domains\Core\Domain;

use Database\Factories\CompositionBudgetComponentFactory;

class CompositionBudgetComponent extends BudgetComponent
{
    protected static ?string $componentType = self::TYPE_COMPOSITION;

    protected $attributes = [
        'type' => self::TYPE_COMPOSITION,
        'total' => 0,
    ];

    protected static function newFactory(): CompositionBudgetComponentFactory
    {
        return CompositionBudgetComponentFactory::new();
    }

    public function getCompositionId(): ?int
    {
        return $this->composition_id;
    }

    public function setCompositionId(?int $compositionId): self
    {
        $this->composition_id = $compositionId;

        return $this;
    }
}
