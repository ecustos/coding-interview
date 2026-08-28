<?php

namespace App\Domains\Core\Domain\Infra\Eloquent;

use App\Domains\Core\Domain\Contracts\InputRepository as InputRepositoryContract;
use App\Domains\Core\Domain\Input;
use App\Domains\Core\Domain\Stage;
use Illuminate\Support\Collection;

class InputRepository implements InputRepositoryContract
{
    public function save(Input $input): Input
    {
        $input->save();

        return $input->refresh();
    }

    public function destroy(Input $input): bool
    {
        return (bool) $input->delete();
    }

    public function getByStage(Stage $stage): Collection
    {
        return Input::query()
            ->whereHas('component', fn ($query) => $query->where('parent_stage_id', $stage->getId()))
            ->orderBy('id')
            ->get();
    }
}
