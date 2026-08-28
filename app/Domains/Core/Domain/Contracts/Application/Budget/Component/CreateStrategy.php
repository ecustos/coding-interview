<?php

namespace App\Domains\Core\Domain\Contracts\Application\Budget\Component;

use App\Domains\Core\Domain\Application\Budget\Component\Create\Command;
use App\Domains\Core\Domain\BudgetComponent;

interface CreateStrategy
{
    public function createComponent(): BudgetComponent;

    public function setFields(Command $command, BudgetComponent $component): BudgetComponent;
}
