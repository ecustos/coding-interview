<?php

namespace App\Domains\Core\Domain\Application\Budget\Index;

use App\Domains\Core\Domain\Contracts\BudgetRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Handler
{
    public function __construct(private BudgetRepository $budgetRepository) {}

    public function handle(int $page = 1, int $perPage = 15): LengthAwarePaginator
    {
        return $this->budgetRepository->index($page, $perPage);
    }
}
