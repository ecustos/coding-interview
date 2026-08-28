<?php

namespace App\Http\Controllers\Stage;

use App\Domains\Core\Domain\Application\Stage\Create\Command;
use App\Domains\Core\Domain\Application\Stage\Create\Handler;
use App\Domains\Core\Domain\Budget;
use App\Http\Controllers\Controller;
use App\Http\Resources\Stage;
use Illuminate\Http\Request;

class CreateController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function store(Request $request, Budget $budget): Stage
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'parent_stage_id' => ['nullable', 'integer', 'exists:stages,id'],
        ]);

        return new Stage($this->handler->handle(new Command(
            $budget,
            $data['description'],
            $data['parent_stage_id'] ?? null,
        )));
    }
}
