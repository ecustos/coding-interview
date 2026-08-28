<?php

namespace App\Http\Controllers\Budget;

use App\Domains\Core\Domain\Application\Budget\Delete\Command;
use App\Domains\Core\Domain\Application\Budget\Delete\Handler;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DeleteController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function destroy(int $budgetId): JsonResponse
    {
        $this->handler->handle(new Command($budgetId));

        return response()->json(null, 204);
    }
}
