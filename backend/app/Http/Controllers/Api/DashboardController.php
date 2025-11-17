<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExpenseReportResource;
use App\Models\ExpenseReport;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics and recent activity.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $organizationId = $user->organization_id;

        // Base query for this organization
        $baseQuery = ExpenseReport::where('organization_id', $organizationId);

        // If user is not an approver, only show their own reports
        if (!$user->canApprove()) {
            $baseQuery->where('user_id', $user->id);
        }

        // Statistics
        $stats = [
            'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
            'submitted' => (clone $baseQuery)->where('status', 'submitted')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            'paid' => (clone $baseQuery)->where('status', 'paid')->count(),
            'total_month' => (clone $baseQuery)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount'),
            'pending_approval' => $user->canApprove()
                ? ExpenseReport::where('organization_id', $organizationId)
                    ->where('status', 'submitted')
                    ->count()
                : 0,
        ];

        // Recent reports
        $recentReports = (clone $baseQuery)
            ->with(['items.category', 'mileageExpenses.vehicle', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'stats' => $stats,
            'recent_reports' => ExpenseReportResource::collection($recentReports),
        ]);
    }

    /**
     * Get monthly expense trends.
     */
    public function trends(Request $request)
    {
        $user = $request->user();
        $months = $request->get('months', 6);

        $baseQuery = ExpenseReport::where('organization_id', $user->organization_id);

        if (!$user->canApprove()) {
            $baseQuery->where('user_id', $user->id);
        }

        $trends = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $total = (clone $baseQuery)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_amount');

            $trends[] = [
                'month' => $date->format('Y-m'),
                'month_name' => $date->translatedFormat('F Y'),
                'total' => $total,
            ];
        }

        return response()->json([
            'trends' => $trends,
        ]);
    }

    /**
     * Get expense breakdown by category.
     */
    public function categoryBreakdown(Request $request)
    {
        $user = $request->user();

        $query = \DB::table('expense_items')
            ->join('expense_reports', 'expense_items.expense_report_id', '=', 'expense_reports.id')
            ->join('expense_categories', 'expense_items.expense_category_id', '=', 'expense_categories.id')
            ->where('expense_reports.organization_id', $user->organization_id)
            ->whereNull('expense_items.deleted_at');

        if (!$user->canApprove()) {
            $query->where('expense_reports.user_id', $user->id);
        }

        $breakdown = $query
            ->select(
                'expense_categories.name',
                'expense_categories.icon',
                'expense_categories.color',
                \DB::raw('COUNT(expense_items.id) as count'),
                \DB::raw('SUM(expense_items.amount) as total')
            )
            ->groupBy('expense_categories.id', 'expense_categories.name', 'expense_categories.icon', 'expense_categories.color')
            ->orderBy('total', 'desc')
            ->get();

        return response()->json([
            'breakdown' => $breakdown,
        ]);
    }
}
