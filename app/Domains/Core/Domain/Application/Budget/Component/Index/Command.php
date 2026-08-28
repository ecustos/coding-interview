<?php

namespace App\Domains\Core\Domain\Application\Budget\Component\Index;

class Command
{
    public function __construct(private int $budgetId) {}

    public function getBudgetId(): int
    {
        return $this->budgetId;
    }
}
