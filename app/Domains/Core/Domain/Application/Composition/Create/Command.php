<?php

namespace App\Domains\Core\Domain\Application\Composition\Create;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\Stage;

class Command
{
    public function __construct(
        private ?Budget $budget,
        private ?Stage $stage,
        private string $description,
        private float $total,
        private ?int $parentStageId = null,
    ) {}

    public function getBudget(): ?Budget
    {
        return $this->budget;
    }

    public function getStage(): ?Stage
    {
        return $this->stage;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getParentStageId(): ?int
    {
        return $this->parentStageId;
    }
}
