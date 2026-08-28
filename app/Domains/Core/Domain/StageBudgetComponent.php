<?php

namespace App\Domains\Core\Domain;

use Database\Factories\StageBudgetComponentFactory;

class StageBudgetComponent extends BudgetComponent
{
    protected static ?string $componentType = self::TYPE_STAGE;

    protected $attributes = [
        'type' => self::TYPE_STAGE,
        'total' => 0,
    ];

    protected static function newFactory(): StageBudgetComponentFactory
    {
        return StageBudgetComponentFactory::new();
    }
}
