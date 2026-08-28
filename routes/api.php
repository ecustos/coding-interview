<?php

use App\Http\Controllers\Budget\CreateController as BudgetCreateController;
use App\Http\Controllers\Budget\DeleteController as BudgetDeleteController;
use App\Http\Controllers\Budget\IndexController as BudgetIndexController;
use App\Http\Controllers\Budget\ShowController as BudgetShowController;
use App\Http\Controllers\Budget\UpdateController as BudgetUpdateController;
use App\Http\Controllers\Composition\CreateController as CompositionCreateController;
use App\Http\Controllers\Composition\DeleteController as CompositionDeleteController;
use App\Http\Controllers\Composition\IndexController as CompositionIndexController;
use App\Http\Controllers\Composition\UpdateController as CompositionUpdateController;
use App\Http\Controllers\Input\CreateController as InputCreateController;
use App\Http\Controllers\Input\DeleteController as InputDeleteController;
use App\Http\Controllers\Input\IndexController as InputIndexController;
use App\Http\Controllers\Input\UpdateController as InputUpdateController;
use App\Http\Controllers\Stage\CreateController as StageCreateController;
use App\Http\Controllers\Stage\DeleteController as StageDeleteController;
use App\Http\Controllers\Stage\IndexController as StageIndexController;
use App\Http\Controllers\Stage\UpdateController as StageUpdateController;
use Illuminate\Support\Facades\Route;

Route::get('budgets', [BudgetIndexController::class, 'index']);
Route::post('budgets', [BudgetCreateController::class, 'store']);
Route::get('budgets/{budget}', [BudgetShowController::class, 'show']);
Route::put('budgets/{budget}', [BudgetUpdateController::class, 'update']);
Route::patch('budgets/{budget}', [BudgetUpdateController::class, 'update']);
Route::delete('budgets/{budget}', [BudgetDeleteController::class, 'destroy']);

Route::get('budgets/{budget}/stages', [StageIndexController::class, 'index']);
Route::post('budgets/{budget}/stages', [StageCreateController::class, 'store']);
Route::put('stages/{stage}', [StageUpdateController::class, 'update']);
Route::delete('stages/{stage}', [StageDeleteController::class, 'destroy']);

Route::get('stages/{stage}/compositions', [CompositionIndexController::class, 'index']);
Route::post('stages/{stage}/compositions', [CompositionCreateController::class, 'storeForStage']);
Route::post('budgets/{budget}/compositions', [CompositionCreateController::class, 'storeForBudget']);
Route::put('compositions/{composition}', [CompositionUpdateController::class, 'update']);
Route::delete('compositions/{composition}', [CompositionDeleteController::class, 'destroy']);

Route::get('stages/{stage}/inputs', [InputIndexController::class, 'index']);
Route::post('stages/{stage}/inputs', [InputCreateController::class, 'storeForStage']);
Route::post('budgets/{budget}/inputs', [InputCreateController::class, 'storeForBudget']);
Route::put('inputs/{input}', [InputUpdateController::class, 'update']);
Route::delete('inputs/{input}', [InputDeleteController::class, 'destroy']);
