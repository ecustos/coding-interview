<?php

namespace App\Domains\Core\Domain\Application\Budget\Update;

use App\Domains\Core\Domain\Budget;

class Command
{
    public function __construct(
        private Budget $budget,
        private string $description,
    ) {}

    public function getBudget(): Budget
    {
        return $this->budget;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
