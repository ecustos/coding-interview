<?php

namespace App\Domains\Core\Domain\Application\Budget\Update;

class Command
{
    public function __construct(
        private int $budgetId,
        private string $description,
    ) {}

    public function getBudgetId(): int
    {
        return $this->budgetId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
