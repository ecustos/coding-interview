<?php

namespace App\Domains\Core\Domain\Infra\Eloquent;

use App\Domains\Core\Domain\Composition;
use App\Domains\Core\Domain\Contracts\CompositionRepository as CompositionRepositoryContract;
use App\Domains\Core\Domain\Stage;
use Illuminate\Support\Collection;

class CompositionRepository implements CompositionRepositoryContract
{
    public function save(Composition $composition): Composition
    {
        $composition->save();

        return $composition->refresh();
    }

    public function destroy(Composition $composition): bool
    {
        return (bool) $composition->delete();
    }

    public function getByStage(Stage $stage): Collection
    {
        return Composition::query()
            ->whereHas('component', fn ($query) => $query->where('parent_stage_id', $stage->getId()))
            ->orderBy('id')
            ->get();
    }
}
