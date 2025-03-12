<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResultController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/user/{sbd}', [ResultController::class, 'getBySBD']);
Route::get('/top10', [ResultController::class, 'getTop10A']);
Route::post('/classify', [ResultController::class, 'classifyScores']);



