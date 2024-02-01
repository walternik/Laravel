<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GiphyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/get_gifs/{query}/{limit?}/{offset?}', [GiphyController::class, 'get_gifs'])->middleware('auth:api');
Route::get('/get_gif_id/{id}', [GiphyController::class, 'get_gif_id'])->middleware('auth:api');
Route::get('/set_fav_gif/{gif_id}/{alias}/{user_id}', [GiphyController::class, 'set_fav_gif'])->middleware('auth:api');