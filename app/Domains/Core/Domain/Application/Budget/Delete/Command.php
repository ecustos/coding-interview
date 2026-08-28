<?php

namespace App\Domains\Core\Domain\Application\Budget\Delete;

class Command
{
    public function __construct(private int $budgetId) {}

    public function getBudgetId(): int
    {
        return $this->budgetId;
    }
}
