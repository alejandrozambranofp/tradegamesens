<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProfileController;
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

// Public Routes
Route::get('guides/top-rated', [GuideController::class, 'topRated']);
Route::get('category-list', [CategoryController::class, 'getList']);
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('games', GameController::class)->only(['index', 'show']);
Route::apiResource('guides', GuideController::class)->only(['index', 'show']);

Route::group(['middleware' => 'auth:sanctum'], function() {

    // Perfil y Usuario Autenticado
    Route::get('/user', [ProfileController::class, 'user']);
    Route::post('/user/profile', [ProfileController::class, 'update']); 

    // Gestión de Usuarios y Roles
    Route::apiResource('users', UserController::class);
    Route::post('users/updateimg', [UserController::class, 'updateimg']);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permissions', PermissionController::class);
    Route::get('role-list', [RoleController::class, 'getList']);
    Route::get('role-permissions/{id}', [PermissionController::class, 'getRolePermissions']);
    Route::put('/role-permissions', [PermissionController::class, 'updateRolePermissions']);

    // Guías
    Route::get('guides/my-guides', [GuideController::class, 'myGuides']);
    Route::get('guides/favorites', [GuideController::class, 'favorites']);
    Route::post('guides/{guide}/favorite', [GuideController::class, 'toggleFavorite']);
    // Re-definimos para incluir store, update, destroy
    Route::apiResource('guides', GuideController::class)->except(['index', 'show']);
    
    // Juegos y Categorías (Admin/Authenticated management)
    Route::apiResource('games', GameController::class)->except(['index', 'show']);
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);

    // Valoraciones
    Route::apiResource('ratings', RatingController::class);

    // Otros Recursos
    Route::apiResource('posts', PostController::class);
    Route::post('images/upload', [ImageController::class, 'upload']);

    // Habilidades/Permisos para el Frontend
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
