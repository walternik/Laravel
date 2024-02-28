<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayController;


Route::post('/play', [PlayController::class, 'play']);
