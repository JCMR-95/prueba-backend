<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RickAndMortyController;

Route::get('/', [RickAndMortyController::class, 'getData'])->name('getData');
Route::get('/getDataFilter', [RickAndMortyController::class, 'getDataFilter'])->name('getDataFilter');
