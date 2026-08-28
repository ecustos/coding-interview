<?php

namespace App\Http\Controllers\Budget\Component;

use App\Domains\Core\Domain\Application\Budget\Component\Index\Command;
use App\Domains\Core\Domain\Application\Budget\Component\Index\Handler;
use App\Http\Controllers\Controller;
use App\Http\Resources\BudgetComponent;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function index(int $budgetId): AnonymousResourceCollection
    {
        return BudgetComponent::collection($this->handler->handle(new Command($budgetId)));
    }
}
