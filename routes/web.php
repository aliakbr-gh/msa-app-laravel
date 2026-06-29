<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\KitchenRecordController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\LedgerModuleController;
use App\Http\Controllers\OrderRecordController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', fn () => view('auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/reports', [ReportsController::class, 'index']);

    Route::middleware('role:superadmin,admin,cashier')->prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'getAllUsers']);
    });
    Route::middleware('role:superadmin,admin')->prefix('users')->group(function () {
        Route::get('/create', [UserController::class, 'createUsersView']);
        Route::post('/create', [UserController::class, 'createUser']);
    });
    Route::middleware('role:superadmin')->prefix('users')->group(function () {
        Route::get('/{id}/edit', [UserController::class, 'editUser']);
        Route::patch('/{id}', [UserController::class, 'updateUser']);
        Route::delete('/{id}', [UserController::class, 'deleteUser']);
    });

    Route::middleware('role:superadmin,cashier')->group(function () {
        Route::post('/{module}/pay', [LedgerController::class, 'pay']);
        Route::post('/orders/{order}/pay', [OrderRecordController::class, 'pay']);
    });

    Route::middleware('role:superadmin,admin,cashier')->prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'index']);
        Route::get('/attendance', [EmployeeController::class, 'attendance']);
        Route::get('/attendance-report', [EmployeeController::class, 'attendanceReport']);
        Route::get('/salaries', [EmployeeController::class, 'salaries']);
        Route::get('/report', [EmployeeController::class, 'report']);
        Route::get('/{employee}/salary-slip', [EmployeeController::class, 'salarySlip']);
    });
    Route::middleware('role:superadmin,admin')->prefix('employees')->group(function () {
        Route::get('/create', [EmployeeController::class, 'create']);
        Route::post('/create', [EmployeeController::class, 'store']);
        Route::post('/attendance', [EmployeeController::class, 'markAttendance']);
        Route::post('/salaries', [EmployeeController::class, 'storeSalaryPayment']);
    });

    Route::middleware('role:superadmin')->prefix('employees')->group(function () {
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit']);
        Route::patch('/{employee}', [EmployeeController::class, 'update']);
        Route::delete('/{employee}', [EmployeeController::class, 'destroy']);
    });

    Route::middleware('role:superadmin,admin,cashier')->prefix('modules')->group(function () {
        Route::get('/', [LedgerModuleController::class, 'index']);
    });
    Route::middleware('role:superadmin,admin')->prefix('modules')->group(function () {
        Route::get('/create', [LedgerModuleController::class, 'create']);
        Route::post('/create', [LedgerModuleController::class, 'store']);
    });
    Route::middleware('role:superadmin')->prefix('modules')->group(function () {
        Route::get('/{module}/edit', [LedgerModuleController::class, 'edit']);
        Route::patch('/{module}', [LedgerModuleController::class, 'update']);
        Route::delete('/{module}', [LedgerModuleController::class, 'destroy']);
    });

    Route::middleware('role:superadmin,admin,cashier')->prefix('orders')->group(function () {
        Route::get('/', [OrderRecordController::class, 'index']);
        Route::get('/{order}/invoice', [OrderRecordController::class, 'invoice']);
    });
    Route::middleware('role:superadmin,admin')->prefix('orders')->group(function () {
        Route::get('/create', [OrderRecordController::class, 'create']);
        Route::post('/create', [OrderRecordController::class, 'store']);
    });
    Route::middleware('role:superadmin')->prefix('orders')->group(function () {
        Route::get('/{order}/edit', [OrderRecordController::class, 'edit']);
        Route::patch('/{order}', [OrderRecordController::class, 'update']);
        Route::delete('/{order}', [OrderRecordController::class, 'destroy']);
    });

    Route::middleware('role:superadmin,admin,cashier')->prefix('kitchen')->group(function () {
        Route::get('/', [KitchenRecordController::class, 'index']);
    });
    Route::middleware('role:superadmin,admin')->prefix('kitchen')->group(function () {
        Route::get('/create', [KitchenRecordController::class, 'create']);
        Route::post('/create', [KitchenRecordController::class, 'store']);
    });
    Route::middleware('role:superadmin')->prefix('kitchen')->group(function () {
        Route::get('/{record}/edit', [KitchenRecordController::class, 'edit']);
        Route::patch('/{record}', [KitchenRecordController::class, 'update']);
        Route::delete('/{record}', [KitchenRecordController::class, 'destroy']);
    });
    Route::middleware('role:superadmin')->get('/backup/download', [BackupController::class, 'download']);

    Route::get('/profile', [AuthController::class, 'profile'])->name('ali');
    Route::patch('/profile/password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('role:superadmin,admin,cashier')->prefix('{module}')->group(function () {
        Route::get('/', [LedgerController::class, 'index']);
    });
    Route::middleware('role:superadmin,admin')->prefix('{module}')->group(function () {
        Route::get('/create', [LedgerController::class, 'create']);
        Route::post('/create', [LedgerController::class, 'store']);
    });
    Route::middleware('role:superadmin')->prefix('{module}')->group(function () {
        Route::get('/{entry}/edit', [LedgerController::class, 'edit']);
        Route::patch('/{entry}', [LedgerController::class, 'update']);
        Route::delete('/{entry}', [LedgerController::class, 'destroy']);
    });
});
