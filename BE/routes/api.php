<?php

use App\Http\Controllers\PostsController;
use App\Http\Controllers\ReelsController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/user', [UserController::class,'store']);
Route::post('/user/login', [UserController::class,'login']);
Route::post('/user/{id}/update', [UserController::class,'update']);

Route::post('/post/Like/{post}', [PostsController::class, 'likes'])->middleware('auth:sanctum');
Route::post('/reel/Like/{reel}', [ReelsController::class, 'likes'])->middleware('auth:sanctum');

    
Route::apiResource('/posts',PostsController::class)->middleware('auth:sanctum');
Route::apiResource('/reels',ReelsController::class)->middleware('auth:sanctum');
