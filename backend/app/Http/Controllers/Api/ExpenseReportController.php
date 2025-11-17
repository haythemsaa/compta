<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExpenseReportResource;
use App\Models\ExpenseReport;
use Illuminate\Http\Request;

class ExpenseReportController extends Controller
{
    public function index(Request $request)
    {
        $query = ExpenseReport::query()
            ->where('organization_id', $request->user()->organization_id)
            ->with(['items.category', 'mileageExpenses.vehicle', 'user']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by user (for managers)
        if ($request->has('user_id') && $request->user()->canApprove()) {
            $query->where('user_id', $request->user_id);
        } else {
            // Employees can only see their own reports
            if (!$request->user()->canApprove()) {
                $query->where('user_id', $request->user()->id);
            }
        }

        $reports = $query->latest()->paginate($request->per_page ?? 15);

        return ExpenseReportResource::collection($reports);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $report = ExpenseReport::create([
            'organization_id' => $request->user()->organization_id,
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'currency' => $request->user()->organization->currency,
        ]);

        return new ExpenseReportResource($report->load(['items', 'mileageExpenses']));
    }

    public function show(Request $request, ExpenseReport $expenseReport)
    {
        $this->authorize('view', $expenseReport);

        return new ExpenseReportResource(
            $expenseReport->load(['items.category', 'items.media', 'mileageExpenses.vehicle', 'user', 'approver', 'rejecter'])
        );
    }

    public function update(Request $request, ExpenseReport $expenseReport)
    {
        $this->authorize('update', $expenseReport);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $expenseReport->update($validated);

        return new ExpenseReportResource($expenseReport->load(['items', 'mileageExpenses']));
    }

    public function destroy(Request $request, ExpenseReport $expenseReport)
    {
        $this->authorize('delete', $expenseReport);

        $expenseReport->delete();

        return response()->json(['message' => 'Note de frais supprimée avec succès']);
    }

    public function submit(Request $request, ExpenseReport $expenseReport)
    {
        $this->authorize('update', $expenseReport);

        if (!$expenseReport->canSubmit()) {
            return response()->json([
                'message' => 'Cette note de frais ne peut pas être soumise',
            ], 422);
        }

        $expenseReport->submit();

        return new ExpenseReportResource($expenseReport);
    }

    public function approve(Request $request, ExpenseReport $expenseReport)
    {
        if (!$request->user()->canApprove()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        if (!$expenseReport->canApprove()) {
            return response()->json([
                'message' => 'Cette note de frais ne peut pas être approuvée',
            ], 422);
        }

        $expenseReport->approve($request->user());

        return new ExpenseReportResource($expenseReport);
    }

    public function reject(Request $request, ExpenseReport $expenseReport)
    {
        if (!$request->user()->canApprove()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        if (!$expenseReport->canReject()) {
            return response()->json([
                'message' => 'Cette note de frais ne peut pas être rejetée',
            ], 422);
        }

        $expenseReport->reject($request->user(), $validated['reason']);

        return new ExpenseReportResource($expenseReport);
    }

    public function pay(Request $request, ExpenseReport $expenseReport)
    {
        if (!$request->user()->isAccountant()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        if ($expenseReport->status !== 'approved') {
            return response()->json([
                'message' => 'Seules les notes approuvées peuvent être payées',
            ], 422);
        }

        $expenseReport->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return new ExpenseReportResource($expenseReport);
    }
}
