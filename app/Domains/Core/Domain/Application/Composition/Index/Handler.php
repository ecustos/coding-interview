<?php

namespace App\Domains\Core\Domain\Application\Composition\Index;

use App\Domains\Core\Domain\Contracts\CompositionRepository;
use Illuminate\Support\Collection;

class Handler
{
    public function __construct(private CompositionRepository $compositionRepository) {}

    public function handle(Command $command): Collection
    {
        return $this->compositionRepository->getByStage($command->getStage());
    }
}
