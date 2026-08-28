<?php

namespace App\Http\Controllers\Budget\Component;

use App\Domains\Core\Domain\Application\Budget\Component\Delete\Command;
use App\Domains\Core\Domain\Application\Budget\Component\Delete\Handler;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DeleteController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function destroy(int $budgetId, int $componentId): JsonResponse
    {
        $this->handler->handle(new Command($budgetId, $componentId));

        return response()->json(null, 204);
    }
}
