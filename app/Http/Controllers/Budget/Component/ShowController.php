<?php

namespace App\Http\Controllers\Budget\Component;

use App\Domains\Core\Domain\Application\Budget\Component\Show\Command;
use App\Domains\Core\Domain\Application\Budget\Component\Show\Handler;
use App\Http\Controllers\Controller;
use App\Http\Resources\BudgetComponent as BudgetComponentResource;

class ShowController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function show(int $budgetId, int $componentId): BudgetComponentResource
    {
        return new BudgetComponentResource($this->handler->handle(new Command($budgetId, $componentId)));
    }
}
