<?php

namespace App\Http\Controllers\Budget;

use App\Domains\Core\Domain\Application\Budget\Show\Command;
use App\Domains\Core\Domain\Application\Budget\Show\Handler;
use App\Domains\Core\Domain\Budget;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ShowController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function show(Budget $budget): JsonResponse
    {
        return response()->json($this->handler->handle(new Command($budget)));
    }
}
