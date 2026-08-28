<?php

namespace App\Http\Controllers\Input;

use App\Domains\Core\Domain\Application\Input\Delete\Command;
use App\Domains\Core\Domain\Application\Input\Delete\Handler;
use App\Domains\Core\Domain\Input;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DeleteController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function destroy(Input $input): JsonResponse
    {
        $this->handler->handle(new Command($input));

        return response()->json(null, 204);
    }
}
