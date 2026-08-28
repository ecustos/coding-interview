<?php

namespace App\Http\Controllers\Budget;

use App\Domains\Core\Domain\Application\Budget\Create\Command;
use App\Domains\Core\Domain\Application\Budget\Create\Handler;
use App\Http\Controllers\Controller;
use App\Http\Resources\Budget;
use Illuminate\Http\Request;

class CreateController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function store(Request $request): Budget
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'max:255'],
        ]);

        return new Budget($this->handler->handle(new Command($data['description'])));
    }
}
