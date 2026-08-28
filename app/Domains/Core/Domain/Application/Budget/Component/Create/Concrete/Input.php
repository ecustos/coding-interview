<?php

namespace App\Domains\Core\Domain\Application\Budget\Component\Create\Concrete;

use App\Domains\Core\Domain\Application\Budget\Component\Create\Command;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\Contracts\Application\Budget\Component\CreateStrategy;
use App\Domains\Core\Domain\InputBudgetComponent;

class Input implements CreateStrategy
{
    public function createComponent(): BudgetComponent
    {
        return new InputBudgetComponent;
    }

    public function setFields(Command $command, BudgetComponent $component): BudgetComponent
    {
        return $component
            ->setBudgetId($command->getBudgetId())
            ->setDescription($command->getDescription())
            ->setInputId($command->getInputId())
            ->setTotal($command->getTotal() ?? 0);
    }
}
