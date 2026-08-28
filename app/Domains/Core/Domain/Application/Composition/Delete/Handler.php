<?php

namespace App\Domains\Core\Domain\Application\Composition\Delete;

use App\Domains\Core\Domain\Contracts\CompositionRepository;
use Illuminate\Support\Facades\DB;

class Handler
{
    public function __construct(private CompositionRepository $compositionRepository) {}

    public function handle(Command $command): void
    {
        DB::transaction(function () use ($command): void {
            $composition = $command->getComposition();

            $composition->component()->delete();
            $this->compositionRepository->destroy($composition);
        });
    }
}
