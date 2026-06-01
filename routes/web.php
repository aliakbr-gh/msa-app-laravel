<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\KitchenRecordController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\OrderRecordController;
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

    Route::middleware('role:superadmin')->group(function () {
        Route::prefix('employees')->group(function () {
            Route::get('/', [EmployeeController::class, 'index']);
            Route::get('/create', [EmployeeController::class, 'create']);
            Route::post('/create', [EmployeeController::class, 'store']);
            Route::get('/attendance', [EmployeeController::class, 'attendance']);
            Route::post('/attendance', [EmployeeController::class, 'markAttendance']);
            Route::get('/salaries', [EmployeeController::class, 'salaries']);
            Route::post('/salaries', [EmployeeController::class, 'storeSalaryPayment']);
            Route::get('/report', [EmployeeController::class, 'report']);
            Route::get('/{employee}/edit', [EmployeeController::class, 'edit']);
            Route::patch('/{employee}', [EmployeeController::class, 'update']);
            Route::delete('/{employee}', [EmployeeController::class, 'destroy']);
        });

        Route::prefix('{module}')->whereIn('module', ['roti', 'beef', 'chicken-1', 'chicken-2'])->group(function () {
            Route::get('/', [LedgerController::class, 'index']);
            Route::get('/create', [LedgerController::class, 'create']);
            Route::post('/create', [LedgerController::class, 'store']);
            Route::get('/{entry}/edit', [LedgerController::class, 'edit']);
            Route::patch('/{entry}', [LedgerController::class, 'update']);
            Route::delete('/{entry}', [LedgerController::class, 'destroy']);
        });

        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderRecordController::class, 'index']);
            Route::get('/create', [OrderRecordController::class, 'create']);
            Route::post('/create', [OrderRecordController::class, 'store']);
            Route::get('/{order}/invoice', [OrderRecordController::class, 'invoice']);
            Route::get('/{order}/edit', [OrderRecordController::class, 'edit']);
            Route::patch('/{order}', [OrderRecordController::class, 'update']);
            Route::delete('/{order}', [OrderRecordController::class, 'destroy']);
        });

        Route::prefix('kitchen')->group(function () {
            Route::get('/', [KitchenRecordController::class, 'index']);
            Route::get('/create', [KitchenRecordController::class, 'create']);
            Route::post('/create', [KitchenRecordController::class, 'store']);
            Route::get('/{record}/edit', [KitchenRecordController::class, 'edit']);
            Route::patch('/{record}', [KitchenRecordController::class, 'update']);
            Route::delete('/{record}', [KitchenRecordController::class, 'destroy']);
        });
    });

    Route::get('/profile', [AuthController::class, 'profile'])->name('ali');
    Route::post('/logout', [AuthController::class, 'logout']);
});
