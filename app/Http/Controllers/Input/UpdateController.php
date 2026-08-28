<?php

namespace App\Http\Controllers\Input;

use App\Domains\Core\Domain\Application\Input\Update\Command;
use App\Domains\Core\Domain\Application\Input\Update\Handler;
use App\Domains\Core\Domain\Input as DomainInput;
use App\Http\Controllers\Controller;
use App\Http\Resources\Input;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function __construct(private Handler $handler) {}

    public function update(Request $request, DomainInput $input): Input
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'total' => ['required', 'numeric', 'min:0'],
        ]);

        return new Input($this->handler->handle(new Command(
            $input,
            $data['description'],
            (float) $data['total'],
        )));
    }
}
