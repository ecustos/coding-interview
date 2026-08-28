<?php

namespace App\Http\Controllers\Budget;

use App\Domains\Core\Domain\Application\Budget\Index\Handler;
use App\Http\Controllers\Controller;
use App\Http\Resources\Budget;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return Budget::collection($this->handler->handle(
            (int) $request->integer('page', 1),
            (int) $request->integer('per_page', 15),
        ));
    }
}
