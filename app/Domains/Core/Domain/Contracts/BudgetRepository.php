<?php

namespace App\Domains\Core\Domain\Contracts;

use App\Domains\Core\Domain\Budget;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BudgetRepository
{
    public function save(Budget $budget): Budget;

    public function index(int $page = 1, int $perPage = 15): LengthAwarePaginator;

    public function getRootStageTotal(Budget $budget): float;

    public function destroy(Budget $budget): bool;

    public function components(Budget $budget): Collection;
}
