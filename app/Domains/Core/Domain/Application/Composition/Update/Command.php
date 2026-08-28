<?php

namespace App\Domains\Core\Domain\Application\Composition\Update;

use App\Domains\Core\Domain\Composition;

class Command
{
    public function __construct(
        private Composition $composition,
        private string $description,
        private float $total,
    ) {}

    public function getComposition(): Composition
    {
        return $this->composition;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTotal(): float
    {
        return $this->total;
    }
}
