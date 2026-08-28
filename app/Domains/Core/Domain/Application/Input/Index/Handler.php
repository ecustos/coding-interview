<?php

namespace App\Domains\Core\Domain\Application\Input\Index;

use App\Domains\Core\Domain\Contracts\InputRepository;
use Illuminate\Support\Collection;

class Handler
{
    public function __construct(private InputRepository $inputRepository) {}

    public function handle(Command $command): Collection
    {
        return $this->inputRepository->getByStage($command->getStage());
    }
}
