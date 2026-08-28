<?php

namespace App\Domains\Core\Domain\Application\Budget\Component\Create;

use App\Domains\Core\Domain\Application\Budget\Component\Create\Concrete\Composition;
use App\Domains\Core\Domain\Application\Budget\Component\Create\Concrete\Input;
use App\Domains\Core\Domain\Application\Budget\Component\Create\Concrete\Stage;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\Contracts\Application\Budget\Component\CreateStrategy;
use Illuminate\Support\Facades\App;
use InvalidArgumentException;

abstract class Factory
{
    public static function create(string $type): CreateStrategy
    {
        return match ($type) {
            BudgetComponent::TYPE_STAGE => App::make(Stage::class),
            BudgetComponent::TYPE_COMPOSITION => App::make(Composition::class),
            BudgetComponent::TYPE_INPUT => App::make(Input::class),
            default => throw new InvalidArgumentException('Unsupported budget component type.'),
        };
    }
}
