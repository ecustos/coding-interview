<?php

namespace Database\Seeders;

use App\Domains\Core\Domain\Input;
use Illuminate\Database\Seeder;

class InputSeeder extends Seeder
{
    public function run(): void
    {
        $inputs = [
            [
                'id' => 1,
                'description' => 'Areia fina',
                'unit_price' => 15,
            ],
            [
                'id' => 2,
                'description' => 'Betorneia',
                'unit_price' => 299,
            ],
        ];

        Input::insert($inputs);
    }
}
