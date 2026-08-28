<?php

namespace App\Domains\Core\Domain\Application\Budget\Component\Create\Concrete;

use App\Domains\Core\Domain\Application\Budget\Component\Create\Command;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\CompositionBudgetComponent;
use App\Domains\Core\Domain\Contracts\Application\Budget\Component\CreateStrategy;

class Composition implements CreateStrategy
{
    public function createComponent(): BudgetComponent
    {
        return new CompositionBudgetComponent;
    }

    public function setFields(Command $command, BudgetComponent $component): BudgetComponent
    {
        return $component
            ->setBudgetId($command->getBudgetId())
            ->setDescription($command->getDescription())
            ->setCompositionId($command->getCompositionId())
            ->setTotal($command->getTotal() ?? 0);
    }
}
