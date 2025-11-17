<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMileageExpenseRequest;
use App\Http\Requests\UpdateMileageExpenseRequest;
use App\Http\Resources\MileageExpenseResource;
use App\Models\ExpenseReport;
use App\Models\MileageExpense;
use Illuminate\Http\Request;

class MileageExpenseController extends Controller
{
    /**
     * Display a listing of mileage expenses for a report.
     */
    public function index(Request $request, ExpenseReport $expenseReport)
    {
        // Vérifier que le rapport appartient à l'organisation de l'utilisateur
        if ($expenseReport->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        $mileageExpenses = $expenseReport->mileageExpenses()
            ->with(['vehicle'])
            ->orderBy('date', 'desc')
            ->get();

        return MileageExpenseResource::collection($mileageExpenses);
    }

    /**
     * Store a newly created mileage expense.
     */
    public function store(StoreMileageExpenseRequest $request, ExpenseReport $expenseReport)
    {
        // Vérifier que le rapport appartient à l'organisation de l'utilisateur
        if ($expenseReport->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que le rapport est toujours modifiable (draft)
        if ($expenseReport->status !== 'draft') {
            abort(422, 'Ce rapport ne peut plus être modifié');
        }

        $mileageExpense = new MileageExpense($request->validated());
        $mileageExpense->expense_report_id = $expenseReport->id;

        // Le calcul automatique se fait dans le model observer (booted)
        $mileageExpense->save();

        return new MileageExpenseResource($mileageExpense->load('vehicle'));
    }

    /**
     * Display the specified mileage expense.
     */
    public function show(Request $request, MileageExpense $mileageExpense)
    {
        // Vérifier l'accès via l'organisation
        if ($mileageExpense->expenseReport->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        return new MileageExpenseResource($mileageExpense->load(['vehicle', 'expenseReport']));
    }

    /**
     * Update the specified mileage expense.
     */
    public function update(UpdateMileageExpenseRequest $request, MileageExpense $mileageExpense)
    {
        // Vérifier l'accès via l'organisation
        if ($mileageExpense->expenseReport->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que le rapport est toujours modifiable
        if ($mileageExpense->expenseReport->status !== 'draft') {
            abort(422, 'Ce rapport ne peut plus être modifié');
        }

        $mileageExpense->update($request->validated());

        // Recalculer le montant si distance ou round_trip changent
        if ($request->has('distance_km') || $request->has('round_trip')) {
            $mileageExpense->calculateAmount();
            $mileageExpense->save();
        }

        return new MileageExpenseResource($mileageExpense->load('vehicle'));
    }

    /**
     * Remove the specified mileage expense.
     */
    public function destroy(Request $request, MileageExpense $mileageExpense)
    {
        // Vérifier l'accès via l'organisation
        if ($mileageExpense->expenseReport->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que le rapport est toujours modifiable
        if ($mileageExpense->expenseReport->status !== 'draft') {
            abort(422, 'Ce rapport ne peut plus être modifié');
        }

        $mileageExpense->delete();

        return response()->json(['message' => 'Frais kilométrique supprimé avec succès'], 200);
    }

    /**
     * Calculate distance between two locations (mock implementation).
     * In production, integrate with Google Maps Distance Matrix API.
     */
    public function calculateDistance(Request $request)
    {
        $request->validate([
            'start_location' => ['required', 'string', 'max:255'],
            'end_location' => ['required', 'string', 'max:255'],
        ]);

        // Mock calculation - in production, use Google Maps API
        $mockDistances = [
            'tunis-sfax' => 272,
            'tunis-sousse' => 140,
            'tunis-monastir' => 162,
            'tunis-bizerte' => 66,
            'tunis-nabeul' => 63,
            'sfax-gabes' => 152,
            'sousse-kairouan' => 55,
        ];

        $start = strtolower(trim($request->start_location));
        $end = strtolower(trim($request->end_location));
        $key = "{$start}-{$end}";
        $reverseKey = "{$end}-{$start}";

        $distance = $mockDistances[$key] ?? $mockDistances[$reverseKey] ?? 50; // Default 50 km

        return response()->json([
            'start_location' => $request->start_location,
            'end_location' => $request->end_location,
            'distance_km' => $distance,
            'status' => 'mock', // 'mock' or 'calculated'
        ]);
    }
}
