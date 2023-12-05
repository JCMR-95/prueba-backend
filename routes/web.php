<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RickAndMortyController;

Route::get('/', [RickAndMortyController::class, 'getData']);
