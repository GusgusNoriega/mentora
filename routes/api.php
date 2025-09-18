<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MediaAssetController;


Route::post('/login', [AuthController::class, 'apiLogin']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:web,api');

// User management routes
Route::middleware(['auth:web,api'])->prefix('users')->group(function () {
    // Perfil del usuario autenticado
    Route::get('/profile', [UserController::class, 'profile']);

    // Cursos inscritos del usuario autenticado
    Route::get('/enrolled-courses', [UserController::class, 'enrolledCourses']);

    // Progreso de cursos del usuario autenticado
    Route::get('/courses-progress', [UserController::class, 'coursesProgress']);

    // Certificados del usuario autenticado
    Route::get('/certificates', [UserController::class, 'certificates']);

    // Ver un usuario específico (propio o si es admin)
    Route::get('/{id}', [UserController::class, 'show']);

    // Cursos inscritos de un usuario específico
    Route::get('/{id}/enrolled-courses', [UserController::class, 'enrolledCourses']);

    // Progreso de cursos de un usuario específico
    Route::get('/{id}/courses-progress', [UserController::class, 'coursesProgress']);

    // Certificados de un usuario específico
    Route::get('/{id}/certificates', [UserController::class, 'certificates']);

    // Actualizar usuario (propio o si es admin)
    Route::put('/{id}', [UserController::class, 'update']);
});

// Admin-only user management routes
Route::middleware(['auth:web,api', 'admin'])->prefix('admin/users')->group(function () {
    // Listar todos los usuarios con filtros
    Route::get('/', [UserController::class, 'index']);

    // Crear nuevo usuario
    Route::post('/', [UserController::class, 'store']);

    // Eliminar usuario
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

// RBAC protected routes (require authentication and admin role)
Route::middleware(['auth:web,api', 'admin'])->prefix('rbac')->group(function () {
    Route::apiResource('roles', RoleController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::apiResource('permissions', PermissionController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::get('roles/{roleId}/permissions', [RolePermissionController::class, 'index']);
    Route::post('roles/{roleId}/permissions/attach', [RolePermissionController::class, 'attach']);
    Route::post('roles/{roleId}/permissions/sync', [RolePermissionController::class, 'sync']);
    Route::post('roles/{roleId}/permissions/detach', [RolePermissionController::class, 'detach']);
});


// =======================
// MEDIA (protegido por Passport)
// =======================
Route::middleware(['auth:web,api'])->prefix('media')->group(function () {
    Route::get('/', [MediaAssetController::class, 'index']);      // GET /api/media
    Route::get('/{id}', [MediaAssetController::class, 'show']);   // GET /api/media/{id}
    Route::post('/', [MediaAssetController::class, 'store']);     // POST /api/media
    Route::patch('/{id}', [MediaAssetController::class, 'update']);// PATCH /api/media/{id}
    Route::delete('/{id}', [MediaAssetController::class, 'destroy']);// DELETE /api/media/{id}
});