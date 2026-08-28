<?php

namespace App\Domains\Core\Domain\Application\Stage\Create;

use App\Domains\Core\Domain\Budget;

class Command
{
    public function __construct(
        private Budget $budget,
        private string $description,
        private ?int $parentStageId = null,
    ) {}

    public function getBudget(): Budget
    {
        return $this->budget;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getParentStageId(): ?int
    {
        return $this->parentStageId;
    }
}
