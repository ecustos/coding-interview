<?php

namespace App\Http\Controllers\Composition;

use App\Domains\Core\Domain\Application\Composition\Update\Command;
use App\Domains\Core\Domain\Application\Composition\Update\Handler;
use App\Domains\Core\Domain\Composition as DomainComposition;
use App\Http\Controllers\Controller;
use App\Http\Resources\Composition;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function update(Request $request, DomainComposition $composition): Composition
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        return new Composition($this->handler->handle(new Command(
            $composition,
            $data['description'],
            (float) $data['total'],
        )));
    }
}
