<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\GuideController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'auth:sanctum'], function() {

    // Perfil y Usuario Autenticado
    Route::get('/user', [ProfileController::class, 'user']);
    Route::post('/user/profile', [ProfileController::class, 'update']); // Usamos POST para manejar archivos correctamente

    // Gestión de Usuarios y Roles
    Route::apiResource('users', UserController::class);
    Route::post('users/updateimg', [UserController::class, 'updateimg']);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permissions', PermissionController::class);
    Route::get('role-list', [RoleController::class, 'getList']);
    Route::get('role-permissions/{id}', [PermissionController::class, 'getRolePermissions']);
    Route::put('/role-permissions', [PermissionController::class, 'updateRolePermissions']);

    // Guías, Juegos y Categorías
    Route::get('guides/my-guides', [GuideController::class, 'myGuides']);
    Route::apiResource('guides', GuideController::class);
    Route::apiResource('games', GameController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::get('category-list', [CategoryController::class, 'getList']);

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