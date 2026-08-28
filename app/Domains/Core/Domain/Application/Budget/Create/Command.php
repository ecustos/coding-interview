<?php

namespace App\Domains\Core\Domain\Application\Budget\Create;

class Command
{
    public function __construct(private string $description) {}

    public function getDescription(): string
    {
        return $this->description;
    }
}
