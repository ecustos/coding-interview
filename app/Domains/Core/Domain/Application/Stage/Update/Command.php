<?php

namespace App\Domains\Core\Domain\Application\Stage\Update;

use App\Domains\Core\Domain\Stage;

class Command
{
    public function __construct(
        private Stage $stage,
        private string $description,
    ) {}

    public function getStage(): Stage
    {
        return $this->stage;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
