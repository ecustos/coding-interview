<?php

namespace App\Domains\Core\Domain\Application\Input\Update;

use App\Domains\Core\Domain\Input;

class Command
{
    public function __construct(
        private Input $input,
        private string $description,
        private float $total,
    ) {}

    public function getInput(): Input
    {
        return $this->input;
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
