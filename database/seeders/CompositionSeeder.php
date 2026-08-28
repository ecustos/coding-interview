<?php

namespace Database\Seeders;

use App\Domains\Core\Domain\Composition;
use Illuminate\Database\Seeder;

class CompositionSeeder extends Seeder
{
    public function run(): void
    {
        $compositions = [
            [
                'id' => 1,
                'description' => 'Cimento top',
                'total' => 100,
            ],
            [
                'id' => 2,
                'description' => 'Tijolos especiais',
                'total' => 200,
            ],
        ];

        Composition::insert($compositions);
    }
}
