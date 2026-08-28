<?php

namespace App\Domains\Core\Domain\Contracts;

use App\Domains\Core\Domain\Composition;
use App\Domains\Core\Domain\Stage;
use Illuminate\Support\Collection;

interface CompositionRepository
{
    public function save(Composition $composition): Composition;

    public function destroy(Composition $composition): bool;

    public function getByStage(Stage $stage): Collection;
}
