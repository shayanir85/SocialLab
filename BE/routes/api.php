<?php

use App\Http\Controllers\PostsController;
use App\Http\Controllers\ReelsController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/user', UserController::class);
Route::post('/user/login', [UserController::class,'login']);


Route::apiResource('/posts',PostsController::class)->middleware('auth:sanctum');
Route::apiResource('/reels',ReelsController::class)->middleware('auth:sanctum');
