<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AvatarController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\GuideController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- RUTAS DE GUÍAS (Orden crucial: Específicas primero) ---
Route::get('guides/top-rated', [GuideController::class, 'topRated']);

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::get('guides/my-guides', [GuideController::class, 'myGuides']);
    Route::get('guides/favorites', [GuideController::class, 'favorites']);
    Route::post('guides/{guide}/favorite', [GuideController::class, 'toggleFavorite']);
    
    Route::post('guides', [GuideController::class, 'store']);
    Route::put('guides/{guide}', [GuideController::class, 'update']);
    Route::delete('guides/{guide}', [GuideController::class, 'destroy']);

    // Admin Specific
    Route::get('admin/guides', [GuideController::class, 'adminIndex']);
    Route::patch('admin/guides/{guide}/status', [GuideController::class, 'updateStatus']);
});

// Rutas públicas de lectura (SIEMPRE después de las específicas)
Route::get('guides', [GuideController::class, 'index']);
Route::get('guides/{idOrSlug}', [GuideController::class, 'show']);


// --- RUTAS DE CATEGORÍAS ---
Route::get('category-list', [CategoryController::class, 'getList']);
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{category}', [CategoryController::class, 'show']);

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{category}', [CategoryController::class, 'update']);
    Route::delete('categories/{category}', [CategoryController::class, 'destroy']);
});


// --- RUTAS DE JUEGOS ---
Route::get('games', [GameController::class, 'index']);
Route::get('games/{game}', [GameController::class, 'show']);

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::post('games', [GameController::class, 'store']);
    Route::put('games/{game}', [GameController::class, 'update']);
    Route::delete('games/{game}', [GameController::class, 'destroy']);
});


// --- RUTAS DE USUARIO Y PERFIL ---
Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::get('/user', [ProfileController::class, 'user']);
    Route::post('/user/profile', [ProfileController::class, 'update']); 
    Route::get('/avatars/predefined', [AvatarController::class, 'getPredefinedAvatars']);
    Route::post('/user/select-avatar', [AvatarController::class, 'selectAvatar']);

    Route::apiResource('users', UserController::class);
    Route::post('users/updateimg', [UserController::class, 'updateimg']);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permissions', PermissionController::class);
    Route::get('role-list', [RoleController::class, 'getList']);
    Route::get('role-permissions/{id}', [PermissionController::class, 'getRolePermissions']);
    Route::put('/role-permissions', [PermissionController::class, 'updateRolePermissions']);

    Route::apiResource('ratings', RatingController::class);
    Route::apiResource('posts', PostController::class);
    Route::post('images/upload', [ImageController::class, 'upload']);

    Route::get('abilities', function(Request $request) {
        return $request->user()->roles()->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->unique()
            ->values()
            ->toArray();
    });
});
