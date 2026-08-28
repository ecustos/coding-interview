<?php

namespace App\Domains\Core\Domain\Application\Composition\Index;

use App\Domains\Core\Domain\Stage;

class Command
{
    public function __construct(private Stage $stage) {}

    public function getStage(): Stage
    {
        return $this->stage;
    }
}
