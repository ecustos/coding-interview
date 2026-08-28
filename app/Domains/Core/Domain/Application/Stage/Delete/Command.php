<?php

namespace App\Domains\Core\Domain\Application\Stage\Delete;

use App\Domains\Core\Domain\Stage;

class Command
{
    public function __construct(private Stage $stage) {}

    public function getStage(): Stage
    {
        return $this->stage;
    }
}
