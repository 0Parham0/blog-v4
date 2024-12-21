<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\BlogExportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FetchSokanDataController;

// user
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/signup', 'signup');
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', 'getUserInfo');
        Route::post('/logout', 'logout');
    });
});

// blog
Route::controller(BlogController::class)->group(function () {
    Route::prefix('/blogs')->group(function () {
        Route::get('/', 'index');
        Route::get('/search', 'search'); // search blogs
        Route::get('/{blog}', 'show'); // show specific blog
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', 'store');
            Route::delete('/{blog}', 'destroy');
            Route::patch('/{blog}', 'update'); // edit intended blog
            Route::post('/{blog}/schedule', 'schedule');
        });
    });
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/blogs', [BlogController::class, 'getUserBlogs']);
});
// blog - like
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/blogs/{blog}/like', [LikeController::class, 'store']);
    Route::post('/blogs/{blog}/unlike', [LikeController::class, 'destroy']);
});

// comment
Route::middleware('auth:sanctum')->post('blogs/{blog}/comments', [CommentController::class, 'store']); // add comment
// comment - like
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/blogs/{blog}/comments/{comment}/like', [LikeController::class, 'store']);
    Route::post('/blogs/{blog}/comments/{comment}/unlike', [LikeController::class, 'destroy']);
});

// admin permissions
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // to see published and unpublished blogs
    Route::get('/admin/blogs', [BlogController::class, 'indexAsAdmin']);

    // export
    Route::get('/admin/blog-exports', [BlogExportController::class, 'index']);
    Route::get('/admin/blog-exports/{fileName}', [BlogExportController::class, 'download']);
});

// Sokan endpoint
Route::get('/sokan-endpoint', [FetchSokanDataController::class, 'index']);

// notifications
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
});

