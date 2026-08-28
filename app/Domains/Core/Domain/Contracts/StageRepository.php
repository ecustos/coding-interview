<?php

namespace App\Domains\Core\Domain\Contracts;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\Stage;
use Illuminate\Support\Collection;

interface StageRepository
{
    public function save(Stage $stage): Stage;

    public function destroy(Stage $stage): bool;

    public function getByBudget(Budget $budget): Collection;
}
