<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Upload a new media file.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,gif'],
            'mediable_type' => ['required', 'string', 'in:App\Models\ExpenseItem,App\Models\Vehicle'],
            'mediable_id' => ['required', 'integer'],
        ]);

        $file = $request->file('file');
        $mediableType = $request->get('mediable_type');
        $mediableId = $request->get('mediable_id');

        // Vérifier que l'entité appartient à l'organisation de l'utilisateur
        $mediable = $mediableType::find($mediableId);

        if (!$mediable) {
            abort(404, 'Ressource introuvable');
        }

        // Vérification de l'organisation selon le type
        if ($mediableType === 'App\Models\ExpenseItem') {
            if ($mediable->expenseReport->organization_id !== $request->user()->organization_id) {
                abort(403, 'Accès non autorisé');
            }
        } elseif ($mediableType === 'App\Models\Vehicle') {
            if ($mediable->organization_id !== $request->user()->organization_id) {
                abort(403, 'Accès non autorisé');
            }
        }

        // Générer un nom unique
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $organizationId = $request->user()->organization_id;
        $path = "organizations/{$organizationId}/media/{$fileName}";

        // Stocker le fichier
        Storage::disk('public')->put($path, file_get_contents($file));

        // Créer l'enregistrement en base
        $media = Media::create([
            'mediable_type' => $mediableType,
            'mediable_id' => $mediableId,
            'organization_id' => $organizationId,
            'uploaded_by' => $request->user()->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'disk' => 'public',
            'ocr_status' => 'pending',
        ]);

        return new MediaResource($media->load('uploader'));
    }

    /**
     * Display the specified media.
     */
    public function show(Request $request, Media $media)
    {
        // Vérifier que le média appartient à l'organisation de l'utilisateur
        if ($media->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        return new MediaResource($media->load(['uploader', 'mediable']));
    }

    /**
     * Download/view the media file.
     */
    public function download(Request $request, Media $media)
    {
        // Vérifier que le média appartient à l'organisation de l'utilisateur
        if ($media->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        if (!Storage::disk($media->disk)->exists($media->file_path)) {
            abort(404, 'Fichier introuvable');
        }

        return Storage::disk($media->disk)->download($media->file_path, $media->file_name);
    }

    /**
     * Delete the specified media.
     */
    public function destroy(Request $request, Media $media)
    {
        // Vérifier que le média appartient à l'organisation de l'utilisateur
        if ($media->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        // Supprimer le fichier physique
        if (Storage::disk($media->disk)->exists($media->file_path)) {
            Storage::disk($media->disk)->delete($media->file_path);
        }

        // Supprimer l'enregistrement
        $media->delete();

        return response()->json(['message' => 'Fichier supprimé avec succès'], 200);
    }

    /**
     * Process OCR for the media file (mock implementation).
     * In production, integrate with Google Cloud Vision or Tesseract.
     */
    public function processOcr(Request $request, Media $media)
    {
        // Vérifier que le média appartient à l'organisation de l'utilisateur
        if ($media->organization_id !== $request->user()->organization_id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que le fichier nécessite de l'OCR
        if (!$media->needsOcr()) {
            abort(422, 'Ce fichier ne nécessite pas d\'OCR ou a déjà été traité');
        }

        // Mock OCR data - in production, use Google Cloud Vision API or Tesseract
        $mockOcrData = [
            'merchant_name' => 'Restaurant Le Gourmet',
            'amount' => 45.50,
            'tva_amount' => 7.27,
            'date' => now()->subDays(2)->format('Y-m-d'),
            'confidence' => 0.87,
            'raw_text' => "RESTAURANT LE GOURMET\nDate: " . now()->subDays(2)->format('d/m/Y') . "\nMontant TTC: 45.50 TND\nTVA 19%: 7.27 TND",
        ];

        $media->update([
            'ocr_data' => $mockOcrData,
            'ocr_status' => 'completed',
            'ocr_processed_at' => now(),
        ]);

        return response()->json([
            'message' => 'OCR traité avec succès',
            'data' => $mockOcrData,
        ]);
    }
}
