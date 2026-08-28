<?php

namespace App\Domains\Core\Domain\Application\Input\Delete;

use App\Domains\Core\Domain\Contracts\InputRepository;
use Illuminate\Support\Facades\DB;

class Handler
{
    public function __construct(private InputRepository $inputRepository) {}

    public function handle(Command $command): void
    {
        DB::transaction(function () use ($command): void {
            $input = $command->getInput();

            $input->component()->delete();
            $this->inputRepository->destroy($input);
        });
    }
}
