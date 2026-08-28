<?php

namespace App\Domains\Core\Domain\Application\Composition\Delete;

use App\Domains\Core\Domain\Composition;

class Command
{
    public function __construct(private Composition $composition) {}

    public function getComposition(): Composition
    {
        return $this->composition;
    }
}
