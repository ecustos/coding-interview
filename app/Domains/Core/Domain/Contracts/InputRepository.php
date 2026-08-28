<?php

namespace App\Domains\Core\Domain\Contracts;

use App\Domains\Core\Domain\Input;
use App\Domains\Core\Domain\Stage;
use Illuminate\Support\Collection;

interface InputRepository
{
    public function save(Input $input): Input;

    public function destroy(Input $input): bool;

    public function getByStage(Stage $stage): Collection;
}
