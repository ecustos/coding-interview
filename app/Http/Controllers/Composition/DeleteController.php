<?php

namespace App\Http\Controllers\Composition;

use App\Domains\Core\Domain\Application\Composition\Delete\Command;
use App\Domains\Core\Domain\Application\Composition\Delete\Handler;
use App\Domains\Core\Domain\Composition;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DeleteController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function destroy(Composition $composition): JsonResponse
    {
        $this->handler->handle(new Command($composition));

        return response()->json(null, 204);
    }
}
