<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\AnnouncementsController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForumPostController;
use App\Http\Controllers\MessageController;
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', function() {
        return \App\Models\User::select('id','name')->where('is_admin',0)->get();
    });
    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);
});

// GET semua post: bebas akses
Route::get('/forum', [ForumPostController::class, 'index']);

// POST post baru & reply: butuh auth
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/forum', [ForumPostController::class, 'store']);
    Route::post('/forum/{post}/reply', [ForumPostController::class, 'reply']);
});


Route::post('/register', [AuthController::class, 'register']);


Route::post('/login', [AuthController::class, 'login']);


Route::options('{any}', function () {
    return response()->noContent();
})->where('any', '.*');


Route::apiResource('news', NewsController::class);
Route::apiResource('announcements', AnnouncementsController::class);
Route::apiResource('events', EventsController::class);
Route::apiResource('gallery', GalleryController::class);
Route::apiResource('communities', CommunityController::class);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

