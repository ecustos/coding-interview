<?php

namespace App\Domains\Core\Domain\Application\Budget\Component\Show;

class Command
{
    public function __construct(
        private int $budgetId,
        private int $componentId,
    ) {}

    public function getBudgetId(): int
    {
        return $this->budgetId;
    }

    public function getComponentId(): int
    {
        return $this->componentId;
    }
}
