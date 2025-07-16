<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('simulation.index');
})->name('simulation.index');
