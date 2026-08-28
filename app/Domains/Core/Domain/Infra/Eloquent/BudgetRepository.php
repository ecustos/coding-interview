<?php

namespace App\Domains\Core\Domain\Infra\Eloquent;

use App\Domains\Core\Domain\Budget;
use App\Domains\Core\Domain\BudgetComponent;
use App\Domains\Core\Domain\Contracts\BudgetRepository as BudgetRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BudgetRepository implements BudgetRepositoryContract
{
    public function find(int $budgetId): ?Budget
    {
        return Budget::query()->find($budgetId);
    }

    public function save(Budget $budget): Budget
    {
        $budget->save();

        return $budget->refresh();
    }

    public function index(int $page = 1, int $perPage = 15): LengthAwarePaginator
    {
        return Budget::query()->orderBy('id')->paginate($perPage, ['*'], 'page', $page);
    }

    public function components(Budget $budget): Collection
    {
        return BudgetComponent::query()
            ->where('budget_id', $budget->getId())
            ->orderBy('id')
            ->get();
    }

    public function destroy(Budget $budget): bool
    {
        return (bool) $budget->delete();
    }
}
