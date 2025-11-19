<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExpenseCategoryController;
use App\Http\Controllers\Api\ExpenseReportController;
use App\Http\Controllers\Api\ExpenseReportExportController;
use App\Http\Controllers\Api\ExpenseItemController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\MileageExpenseController;
use App\Http\Controllers\Api\MediaController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    // Expense Reports
    Route::apiResource('expense-reports', ExpenseReportController::class);
    Route::post('/expense-reports/{expenseReport}/submit', [ExpenseReportController::class, 'submit']);
    Route::post('/expense-reports/{expenseReport}/approve', [ExpenseReportController::class, 'approve']);
    Route::post('/expense-reports/{expenseReport}/reject', [ExpenseReportController::class, 'reject']);
    Route::post('/expense-reports/{expenseReport}/pay', [ExpenseReportController::class, 'pay']);

    // Expense Reports Export
    Route::get('/expense-reports/{expenseReport}/export/pdf', [ExpenseReportExportController::class, 'exportPDF']);
    Route::get('/expense-reports/{expenseReport}/export/excel', [ExpenseReportExportController::class, 'exportExcel']);
    Route::post('/expense-reports/export/excel', [ExpenseReportExportController::class, 'exportMultipleExcel']);

    // Expense Items
    Route::apiResource('expense-reports.items', ExpenseItemController::class)->shallow();

    // Expense Categories
    Route::get('/expense-categories', [ExpenseCategoryController::class, 'index']);
    Route::get('/expense-categories/{expenseCategory}', [ExpenseCategoryController::class, 'show']);

    // Vehicles
    Route::apiResource('vehicles', VehicleController::class);
    Route::get('/vehicles/{vehicle}/mileage-rate', [VehicleController::class, 'getMileageRate']);

    // Mileage Expenses
    Route::apiResource('expense-reports.mileage-expenses', MileageExpenseController::class)->shallow();
    Route::post('/mileage/calculate-distance', [MileageExpenseController::class, 'calculateDistance']);

    // Media
    Route::post('/media/upload', [MediaController::class, 'upload']);
    Route::get('/media/{media}', [MediaController::class, 'show']);
    Route::get('/media/{media}/download', [MediaController::class, 'download']);
    Route::delete('/media/{media}', [MediaController::class, 'destroy']);
    Route::post('/media/{media}/ocr', [MediaController::class, 'processOcr']);

    // Dashboard & Reports
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/trends', [DashboardController::class, 'trends']);
    Route::get('/dashboard/category-breakdown', [DashboardController::class, 'categoryBreakdown']);
});
