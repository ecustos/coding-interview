<?php

namespace App\Domains\Core\Domain\Application\Input\Index;

use App\Domains\Core\Domain\Stage;

class Command
{
    public function __construct(private Stage $stage) {}

    public function getStage(): Stage
    {
        return $this->stage;
    }
}
