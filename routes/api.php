<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// protected
Route::group(['middleware' => ['auth:sanctum']], function () {


    // user
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [AuthController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);


    // post blog
    Route::get('/posts', [PostController::class, 'index']);    // tampil semua blog
    Route::post('/posts', [PostController::class, 'store']);    // tambah
    Route::get('/posts/{id}', [PostController::class, 'show']);  // tampil berdasarkan id
    Route::put('/posts/{id}', [PostController::class, 'update']);     // edit
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);   // hapus

    // Comment
    Route::get('/posts/{id}/comments', [CommentController::class, 'index']);    // tampil comment by id
    Route::post('/posts/{id}/comments', [CommentController::class, 'store']);    // tambah
    Route::put('/comments/{id}', [CommentController::class, 'update']);     // edit
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);   // hapus

    // Like
    Route::post('/posts/{id}/likes', [LikeController::class, 'likeOrUnlike']);    // like

});
