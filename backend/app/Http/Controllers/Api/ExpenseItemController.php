<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseItemRequest;
use App\Http\Requests\UpdateExpenseItemRequest;
use App\Http\Resources\ExpenseItemResource;
use App\Models\ExpenseItem;
use App\Models\ExpenseReport;
use Illuminate\Http\Request;

class ExpenseItemController extends Controller
{
    /**
     * Display a listing of expense items for a report.
     */
    public function index(Request $request, ExpenseReport $expenseReport)
    {
        // Vérifier que le rapport appartient à l'organisation de l'utilisateur
        if ($expenseReport->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        $items = $expenseReport->items()
            ->with(['category', 'media'])
            ->orderBy('date', 'desc')
            ->get();

        return ExpenseItemResource::collection($items);
    }

    /**
     * Store a newly created expense item.
     */
    public function store(StoreExpenseItemRequest $request, ExpenseReport $expenseReport)
    {
        // Vérifier que le rapport appartient à l'organisation de l'utilisateur
        if ($expenseReport->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que le rapport est toujours modifiable (draft)
        if ($expenseReport->status !== 'draft') {
            abort(422, 'Ce rapport ne peut plus être modifié');
        }

        $item = new ExpenseItem($request->validated());
        $item->expense_report_id = $expenseReport->id;

        // Auto-calculate TVA
        $defaultRate = $request->user()->organization->default_tva_rate ?? 19.00;
        $item->calculateTVA($defaultRate);

        $item->save();

        return new ExpenseItemResource($item->load(['category', 'media']));
    }

    /**
     * Display the specified expense item.
     */
    public function show(Request $request, ExpenseItem $expenseItem)
    {
        // Vérifier l'accès via l'organisation
        if ($expenseItem->expenseReport->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        return new ExpenseItemResource($expenseItem->load(['category', 'media', 'expenseReport']));
    }

    /**
     * Update the specified expense item.
     */
    public function update(UpdateExpenseItemRequest $request, ExpenseItem $expenseItem)
    {
        // Vérifier l'accès via l'organisation
        if ($expenseItem->expenseReport->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que le rapport est toujours modifiable
        if ($expenseItem->expenseReport->status !== 'draft') {
            abort(422, 'Ce rapport ne peut plus être modifié');
        }

        $expenseItem->update($request->validated());

        // Recalculer la TVA si nécessaire
        if ($request->has('amount') || $request->has('tva_rate')) {
            $defaultRate = $request->user()->organization->default_tva_rate ?? 19.00;
            $expenseItem->calculateTVA($defaultRate);
            $expenseItem->save();
        }

        return new ExpenseItemResource($expenseItem->load(['category', 'media']));
    }

    /**
     * Remove the specified expense item.
     */
    public function destroy(Request $request, ExpenseItem $expenseItem)
    {
        // Vérifier l'accès via l'organisation
        if ($expenseItem->expenseReport->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que le rapport est toujours modifiable
        if ($expenseItem->expenseReport->status !== 'draft') {
            abort(422, 'Ce rapport ne peut plus être modifié');
        }

        $expenseItem->delete();

        return response()->json(['message' => 'Dépense supprimée avec succès'], 200);
    }
}
