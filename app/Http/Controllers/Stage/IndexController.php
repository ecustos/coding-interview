<?php

namespace App\Http\Controllers\Stage;

use App\Domains\Core\Domain\Application\Stage\Index\Command;
use App\Domains\Core\Domain\Application\Stage\Index\Handler;
use App\Domains\Core\Domain\Budget;
use App\Http\Controllers\Controller;
use App\Http\Resources\Stage;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function index(Budget $budget): AnonymousResourceCollection
    {
        return Stage::collection($this->handler->handle(new Command($budget)));
    }
}
