<?php

namespace App\Http\Controllers\Budget;

use App\Domains\Core\Domain\Application\Budget\Delete\Command;
use App\Domains\Core\Domain\Application\Budget\Delete\Handler;
use App\Domains\Core\Domain\Budget;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DeleteController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function destroy(Budget $budget): JsonResponse
    {
        $this->handler->handle(new Command($budget));

        return response()->json(null, 204);
    }
}
