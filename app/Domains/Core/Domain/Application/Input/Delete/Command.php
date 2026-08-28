<?php

namespace App\Domains\Core\Domain\Application\Input\Delete;

use App\Domains\Core\Domain\Input;

class Command
{
    public function __construct(private Input $input) {}

    public function getInput(): Input
    {
        return $this->input;
    }
}
