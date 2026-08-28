<?php

namespace App\Http\Controllers\Composition;

use App\Domains\Core\Domain\Application\Composition\Index\Command;
use App\Domains\Core\Domain\Application\Composition\Index\Handler;
use App\Domains\Core\Domain\Stage;
use App\Http\Controllers\Controller;
use App\Http\Resources\Composition;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function index(Stage $stage): AnonymousResourceCollection
    {
        return Composition::collection($this->handler->handle(new Command($stage)));
    }
}
