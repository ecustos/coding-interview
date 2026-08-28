<?php

namespace App\Domains\Core\Domain\Application\Budget\Component\Create\Concrete;

use App\Domains\Core\Domain\Application\Budget\Component\Create\Command;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\Contracts\Application\Budget\Component\CreateStrategy;
use App\Domains\Core\Domain\StageBudgetComponent;

class Stage implements CreateStrategy
{
    public function createComponent(): BudgetComponent
    {
        return new StageBudgetComponent;
    }

    public function setFields(Command $command, BudgetComponent $component): BudgetComponent
    {
        return $component
            ->setBudgetId($command->getBudgetId())
            ->setDescription($command->getDescription())
            ->setTotal($command->getTotal() ?? 0);
    }
}
