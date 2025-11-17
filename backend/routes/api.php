<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExpenseReportController;
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

    // Expense Items
    Route::apiResource('expense-reports.items', ExpenseItemController::class)->shallow();

    // Vehicles
    Route::apiResource('vehicles', VehicleController::class);

    // Mileage Expenses
    Route::apiResource('expense-reports.mileage-expenses', MileageExpenseController::class)->shallow();
    Route::post('/mileage/calculate-distance', [MileageExpenseController::class, 'calculateDistance']);

    // Media
    Route::post('/media/upload', [MediaController::class, 'upload']);
    Route::get('/media/{media}', [MediaController::class, 'show']);
    Route::delete('/media/{media}', [MediaController::class, 'destroy']);
    Route::post('/media/{media}/ocr', [MediaController::class, 'processOcr']);

    // Dashboard & Reports
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return response()->json([
            'stats' => [
                'pending' => $user->expenseReports()->draft()->count(),
                'submitted' => $user->expenseReports()->submitted()->count(),
                'approved' => $user->expenseReports()->approved()->count(),
                'rejected' => $user->expenseReports()->rejected()->count(),
                'total_month' => $user->expenseReports()
                    ->whereMonth('created_at', now()->month)
                    ->sum('total_amount'),
            ],
            'recent_reports' => $user->expenseReports()
                ->with(['items', 'mileageExpenses'])
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    });
});
