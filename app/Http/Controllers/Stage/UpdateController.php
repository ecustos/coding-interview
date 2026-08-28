<?php

namespace App\Http\Controllers\Stage;

use App\Domains\Core\Domain\Application\Stage\Update\Command;
use App\Domains\Core\Domain\Application\Stage\Update\Handler;
use App\Domains\Core\Domain\Stage as DomainStage;
use App\Http\Controllers\Controller;
use App\Http\Resources\Stage;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function update(Request $request, DomainStage $stage): Stage
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'max:255'],
        ]);

        return new Stage($this->handler->handle(new Command($stage, $data['description'])));
    }
}
