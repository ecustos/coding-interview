<?php

namespace Database\Seeders;

use App\Domains\Core\Domain\Application\Budget\Create\Command as CreateBudgetCommand;
use App\Domains\Core\Domain\Application\Budget\Create\Handler as CreateBudgetHandler;
use App\Domains\Core\Domain\Application\Composition\Create\Command as CreateCompositionCommand;
use App\Domains\Core\Domain\Application\Composition\Create\Handler as CreateCompositionHandler;
use App\Domains\Core\Domain\Application\Input\Create\Command as CreateInputCommand;
use App\Domains\Core\Domain\Application\Input\Create\Handler as CreateInputHandler;
use App\Domains\Core\Domain\Application\Stage\Create\Command as CreateStageCommand;
use App\Domains\Core\Domain\Application\Stage\Create\Handler as CreateStageHandler;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $budget = app(CreateBudgetHandler::class)->handle(new CreateBudgetCommand('Reforma'));

        $stage1 = app(CreateStageHandler::class)->handle(new CreateStageCommand($budget, 'Stage 1'));
        $stage11 = app(CreateStageHandler::class)->handle(new CreateStageCommand($budget, 'Stage 1.1', $stage1->id));

        app(CreateInputHandler::class)->handle(new CreateInputCommand(null, $stage11, 'Argamassa', 120));

        app(CreateCompositionHandler::class)->handle(new CreateCompositionCommand(null, $stage1, 'Composition 1.1', 350));

        app(CreateCompositionHandler::class)->handle(new CreateCompositionCommand(null, $stage1, 'Composition 1.2', 220));

        app(CreateInputHandler::class)->handle(new CreateInputCommand(null, $stage1, 'Input 1.1', 80));

        app(CreateInputHandler::class)->handle(new CreateInputCommand(null, $stage1, 'Input 1.2', 50));

        $stage2 = app(CreateStageHandler::class)->handle(new CreateStageCommand($budget, 'Stage 2'));
        $stage21 = app(CreateStageHandler::class)->handle(new CreateStageCommand($budget, 'Stage 2.1', $stage2->id));

        app(CreateInputHandler::class)->handle(new CreateInputCommand(null, $stage21, 'Cimento', 90));

        app(CreateCompositionHandler::class)->handle(new CreateCompositionCommand(null, $stage2, 'Composition 2.1', 410));

        app(CreateInputHandler::class)->handle(new CreateInputCommand(null, $stage2, 'Input 2.1', 70));
    }
}
