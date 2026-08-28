<?php

namespace App\Http\Controllers\Input;

use App\Domains\Core\Domain\Application\Input\Index\Command;
use App\Domains\Core\Domain\Application\Input\Index\Handler;
use App\Domains\Core\Domain\Stage;
use App\Http\Controllers\Controller;
use App\Http\Resources\Input;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function index(Stage $stage): AnonymousResourceCollection
    {
        return Input::collection($this->handler->handle(new Command($stage)));
    }
}
