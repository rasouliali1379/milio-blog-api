<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;



Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('index', [UserController::class, 'index']);
    Route::post('logout', [UserController::class, 'logout']);
});

Route::prefix('/categories')->group(function (){
    Route::post('/add', [CategoryController::class, 'add'])->middleware('auth:api');;
    Route::post('/update', [CategoryController::class, 'update'])->middleware('auth:api');
    Route::post('/delete', [CategoryController::class, 'delete'])->middleware('auth:api');
    Route::get('/all', [CategoryController::class, 'all'])->middleware('auth:api');
    Route::get('/index', [CategoryController::class, 'index']);

    Route::any('/{any}', function () {
        return response()->json([
            'message' => 'Wrong route or method'
        ],404);
    });
});

Route::prefix('/comments')->group(function (){
    Route::post('/add', [CommentController::class, 'add'])->middleware('auth:api');;
    Route::post('/delete', [CommentController::class, 'delete'])->middleware('auth:api');;
    Route::post('/confirm', [CommentController::class, 'confirm'])->middleware('auth:api');;
    Route::post('/reply', [CommentController::class, 'reply'])->middleware('auth:api');;
    Route::get('/all', [CommentController::class, 'all'])->middleware('auth:api');;

    Route::any('/{any}', function () {
        return response()->json([
            'message' => 'Wrong route or method'
        ],404);
    });
});

Route::prefix('/contact')->group(function (){
    Route::post('/add', [ContactUsController::class, 'add']);;
    Route::post('/seen', [ContactUsController::class, 'seen'])->middleware('auth:api');;
    Route::get('/all', [ContactUsController::class, 'all'])->middleware('auth:api');;

    Route::any('/{any}', function () {
        return response()->json([
            'message' => 'Wrong route or method'
        ],404);
    });
});

Route::prefix('/posts')->group(function (){
    Route::post('/add', [PostController::class, 'add'])->middleware('auth:api');;
    Route::post('/update', [PostController::class, 'update'])->middleware('auth:api');;
    Route::post('/delete', [PostController::class, 'delete'])->middleware('auth:api');;
    Route::get('/all', [PostController::class, 'all'])->middleware('auth:api');;
    Route::get('/{id}', [PostController::class, 'post'])->middleware('auth:api');;

    Route::any('/{any}', function () {
        return response()->json([
            'message' => 'Wrong route or method'
        ],404);
    });
});

Route::any('/{any}', function () {
    return response()->json([
        'message' => 'Wrong route or method'
    ],404);
});
