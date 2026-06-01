<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', fn () => view('auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/setup-project', [SetupController::class, 'setupProject']);
    Route::post('/setup-project', [SetupController::class, 'addSU']);
});

Route::middleware('role:superadmin')->prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'getAllRoles']);
    Route::get('/{id}/edit', [RoleController::class, 'editRole']);
    Route::patch('/{id}', [RoleController::class, 'updateRole']);
    Route::delete('/{id}', [RoleController::class, 'deleteRole']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard.dashboard'));

    Route::middleware('role:superadmin')->prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'getAllUsers']);
        Route::get('/{id}/edit', [UserController::class, 'editUser']);
        Route::patch('/{id}', [UserController::class, 'updateUser']);
        Route::delete('/{id}', [UserController::class, 'deleteUser']);
        Route::get('/create', [UserController::class, 'createUsersView']);
        Route::post('/create', [UserController::class, 'createUser']);
    });

    Route::middleware('role:superadmin')->prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'getAllRoles']);
        Route::get('/{id}/edit', [RoleController::class, 'editRole']);
        Route::patch('/{id}', [RoleController::class, 'updateRole']);
        Route::delete('/{id}', [RoleController::class, 'deleteRole']);
        Route::get('/create', [RoleController::class, 'createRolesView']);
        Route::post('/create', [RoleController::class, 'createRole']);
    });

    Route::get('/profile', [AuthController::class, 'profile'])->name('ali');
    Route::post('/logout', [AuthController::class, 'logout']);
});
