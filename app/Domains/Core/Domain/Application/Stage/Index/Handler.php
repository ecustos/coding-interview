<?php

namespace App\Domains\Core\Domain\Application\Stage\Index;

use App\Domains\Core\Domain\Contracts\StageRepository;
use Illuminate\Support\Collection;

class Handler
{
    public function __construct(private StageRepository $stageRepository) {}

    public function handle(Command $command): Collection
    {
        return $this->stageRepository->getByBudget($command->getBudget());
    }
}
