<?php

namespace App\Domains\Core\Domain\Application\Budget\Show;

use App\Domains\Core\Domain\Budget;

class Command
{
    public function __construct(private Budget $budget) {}

    public function getBudget(): Budget
    {
        return $this->budget;
    }
}
