<?php

use App\Http\Controllers\Budget\Component\CreateController as BudgetComponentCreateController;
use App\Http\Controllers\Budget\Component\DeleteController as BudgetComponentDeleteController;
use App\Http\Controllers\Budget\Component\IndexController as BudgetComponentIndexController;
use App\Http\Controllers\Budget\Component\ShowController as BudgetComponentShowController;
use App\Http\Controllers\Budget\Component\UpdateController as BudgetComponentUpdateController;
use App\Http\Controllers\Budget\CreateController as BudgetCreateController;
use App\Http\Controllers\Budget\DeleteController as BudgetDeleteController;
use App\Http\Controllers\Budget\IndexController as BudgetIndexController;
use App\Http\Controllers\Budget\ShowController as BudgetShowController;
use App\Http\Controllers\Budget\UpdateController as BudgetUpdateController;
use Illuminate\Support\Facades\Route;

Route::get('budgets', [BudgetIndexController::class, 'index']);
Route::post('budgets', [BudgetCreateController::class, 'store']);
Route::get('budgets/{budgetId}', [BudgetShowController::class, 'show']);
Route::put('budgets/{budgetId}', [BudgetUpdateController::class, 'update']);
Route::delete('budgets/{budgetId}', [BudgetDeleteController::class, 'destroy']);

Route::get('budget/{budgetId}/component', [BudgetComponentIndexController::class, 'index']);
Route::post('budget/{budgetId}/component', [BudgetComponentCreateController::class, 'store']);
Route::get('budget/{budgetId}/component/{componentId}', [BudgetComponentShowController::class, 'show']);
Route::put('budget/{budgetId}/component/{componentId}', [BudgetComponentUpdateController::class, 'update']);
Route::delete('budget/{budgetId}/component/{componentId}', [BudgetComponentDeleteController::class, 'destroy']);
