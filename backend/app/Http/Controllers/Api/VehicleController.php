<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of vehicles.
     */
    public function index(Request $request)
    {
        $query = Vehicle::query()
            ->where('organization_id', $request->user()->organization_id)
            ->with(['user']);

        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Filter by user (personal vehicles)
        if ($request->has('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        // Filter by type (personal or company)
        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        $vehicles = $query->orderBy('name')->get();

        return VehicleResource::collection($vehicles);
    }

    /**
     * Store a newly created vehicle.
     */
    public function store(StoreVehicleRequest $request)
    {
        $vehicle = new Vehicle($request->validated());
        $vehicle->organization_id = $request->user()->organization_id;

        // Si le véhicule est personnel et qu'aucun user_id n'est fourni, attribuer à l'utilisateur actuel
        if ($vehicle->type === 'personal' && !$vehicle->user_id) {
            $vehicle->user_id = $request->user()->id;
        }

        $vehicle->save();

        return new VehicleResource($vehicle->load('user'));
    }

    /**
     * Display the specified vehicle.
     */
    public function show(Request $request, Vehicle $vehicle)
    {
        // Vérifier que le véhicule appartient à l'organisation de l'utilisateur
        if ($vehicle->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        return new VehicleResource($vehicle->load(['user', 'mileageExpenses']));
    }

    /**
     * Update the specified vehicle.
     */
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        // Vérifier que le véhicule appartient à l'organisation de l'utilisateur
        if ($vehicle->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        $vehicle->update($request->validated());

        return new VehicleResource($vehicle->load('user'));
    }

    /**
     * Remove the specified vehicle.
     */
    public function destroy(Request $request, Vehicle $vehicle)
    {
        // Vérifier que le véhicule appartient à l'organisation de l'utilisateur
        if ($vehicle->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier qu'il n'y a pas de frais kilométriques associés
        if ($vehicle->mileageExpenses()->count() > 0) {
            abort(422, 'Impossible de supprimer ce véhicule car il est utilisé dans des frais kilométriques');
        }

        $vehicle->delete();

        return response()->json(['message' => 'Véhicule supprimé avec succès'], 200);
    }

    /**
     * Get mileage rate for a vehicle.
     */
    public function getMileageRate(Request $request, Vehicle $vehicle)
    {
        // Vérifier que le véhicule appartient à l'organisation de l'utilisateur
        if ($vehicle->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'annual_km' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);

        $annualKm = $request->get('annual_km', 0);
        $rate = $vehicle->getMileageRate($annualKm);

        return response()->json([
            'vehicle_id' => $vehicle->id,
            'fiscal_power' => $vehicle->fiscal_power,
            'annual_km' => $annualKm,
            'rate' => $rate,
            'currency' => 'TND',
        ]);
    }
}
