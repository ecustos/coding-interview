<?php

namespace App\Domains\Core\Domain\Application\Budget\Component\Create;

class Command
{
    public function __construct(
        private int $budgetId,
        private string $type,
        private string $description,
        private ?float $total = null,
        private ?int $compositionId = null,
        private ?int $inputId = null,
    ) {}

    public function getBudgetId(): int
    {
        return $this->budgetId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function getCompositionId(): ?int
    {
        return $this->compositionId;
    }

    public function getInputId(): ?int
    {
        return $this->inputId;
    }
}
