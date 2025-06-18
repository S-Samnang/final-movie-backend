<?php

use App\Http\Controllers\ActorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Api\MovieApiController;
use App\Http\Controllers\Api\MovieSearchController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RatingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider and assigned to the
| "api" middleware group. Enjoy building your API!
|
*/



Route::apiResource('/roles', RoleController::class);
Route::apiResource('/permissions', PermissionController::class);
// Authenticated User Info (Sanctum, optional)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ==============================
// Auth Routes (JWT)
// ==============================
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'store']);
Route::get('/auth/user/{id}', [AuthController::class, 'show']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/auth/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('auth/request-password-reset', [AuthController::class, 'requestPasswordReset']);
Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);
// Route::middleware('auth:api')->get('/me', [AuthController::class, 'me']);
// Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);

// ==============================
// Social Login (Google & GitHub)
// ==============================
Route::prefix('auth')->group(function () {
    Route::get('/google', [SocialAuthController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

    Route::get('/github', [SocialAuthController::class, 'redirectToGitHub']);
    Route::get('/github/callback', [SocialAuthController::class, 'handleGitHubCallback']);
});

// ==============================
// Movie Routes
// ==============================
Route::get('/movies', [MovieApiController::class, 'index']);
Route::get('/movies/by-id/{id}', [MovieApiController::class, 'showById']);
Route::get('/movies/by-tmdb/{tmdb_id}', [MovieApiController::class, 'showByTmdb']);
Route::get('/movies/{tmdb_id}/trailer', [MovieApiController::class, 'getTrailer']);
Route::get('/actor/{id}', [ActorController::class, 'show']);
Route::get('/ratings/{movieId}', [RatingController::class, 'index']);
Route::middleware(['auth:api'])->group(function () {
    Route::post('/movies', [MovieApiController::class, 'store']);
    Route::put('/movies/{id}', [MovieApiController::class, 'update']);
    Route::delete('/movies/{id}', [MovieApiController::class, 'destroy']);

    Route::post('/favorites/toggle', [MovieApiController::class, 'toggleFavorite']);
    Route::get('/movies/favorites', [MovieApiController::class, 'getFavorites']);
    Route::post('/ratings', [RatingController::class, 'store']);
});

// ==============================
// Search & Genre Routes
// ==============================
Route::get('/search', [MovieSearchController::class, 'search']);
Route::get('/genres', fn() => \App\Models\Genre::select('id', 'name')->orderBy('name')->get());


Route::middleware('auth:api')->group(function () {
    Route::get('/me', [UserController::class, 'me']);
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users/{id}/role', [UserController::class, 'updateRole']);
});