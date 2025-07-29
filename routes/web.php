<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SimulationController;
use App\Http\Controllers\SubjectOrderController;
use App\Http\Controllers\ConvalidationController;

Route::get('/', function () {
    return view('simulation.index');
})->name('simulation.index');

// Simulation routes
Route::post('/simulation/analyze-impact', [SimulationController::class, 'analyzeImpact'])->name('simulation.analyzeImpact');
Route::get('/simulation/original-order', [SubjectOrderController::class, 'getOriginalOrderJson'])->name('simulation.originalOrder');

// Convalidation routes
Route::group(['prefix' => 'convalidation'], function () {
    Route::get('/', [ConvalidationController::class, 'index'])->name('convalidation.index');
    Route::get('/create', [ConvalidationController::class, 'create'])->name('convalidation.create');
    Route::post('/', [ConvalidationController::class, 'store'])->name('convalidation.store');
    Route::get('/{externalCurriculum}', [ConvalidationController::class, 'show'])->name('convalidation.show');
    Route::delete('/{externalCurriculum}', [ConvalidationController::class, 'destroy'])->name('convalidation.destroy');
    Route::get('/{externalCurriculum}/export', [ConvalidationController::class, 'exportReport'])->name('convalidation.export');
    
    // New route for impact analysis
    Route::post('/{externalCurriculum}/analyze-impact', [ConvalidationController::class, 'analyzeConvalidationImpact'])->name('convalidation.analyze-impact');
    
    // Convalidation management
    Route::post('/convalidation', [ConvalidationController::class, 'storeConvalidation'])->name('convalidation.store-convalidation');
    Route::delete('/convalidation/{convalidation}', [ConvalidationController::class, 'destroyConvalidation'])->name('convalidation.destroy-convalidation');
    Route::get('/suggestions', [ConvalidationController::class, 'getSuggestions'])->name('convalidation.suggestions');
});
