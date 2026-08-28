<?php

namespace App\Http\Controllers\Stage;

use App\Domains\Core\Domain\Application\Stage\Delete\Command;
use App\Domains\Core\Domain\Application\Stage\Delete\Handler;
use App\Domains\Core\Domain\Stage;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DeleteController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function destroy(Stage $stage): JsonResponse
    {
        $this->handler->handle(new Command($stage));

        return response()->json(null, 204);
    }
}
