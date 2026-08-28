<?php

namespace App\Http\Controllers\Input;

use App\Domains\Core\Domain\Application\Input\Create\Command;
use App\Domains\Core\Domain\Application\Input\Create\Handler;
use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\Stage;
use App\Http\Controllers\Controller;
use App\Http\Resources\Input;
use Illuminate\Http\Request;

class CreateController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function storeForStage(Request $request, Stage $stage): Input
    {
        $data = $this->validated($request);

        return new Input($this->handler->handle(new Command(
            null,
            $stage,
            $data['description'],
            (float) $data['total'],
        )));
    }

    public function storeForBudget(Request $request, Budget $budget): Input
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'total' => ['required', 'numeric', 'min:0'],
            'parent_stage_id' => ['required', 'integer', 'exists:stages,id'],
        ]);

        return new Input($this->handler->handle(new Command(
            $budget,
            null,
            $data['description'],
            (float) $data['total'],
            $data['parent_stage_id'],
        )));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'total' => ['required', 'numeric', 'min:0'],
        ]);
    }
}
