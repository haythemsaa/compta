<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpenseReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExpenseReportExportController extends Controller
{
    /**
     * Export expense report as PDF
     */
    public function exportPDF(Request $request, $id)
    {
        $report = ExpenseReport::with([
            'user',
            'organization',
            'items.category',
            'mileageExpenses.vehicle',
            'approver',
            'rejecter'
        ])->findOrFail($id);

        // Authorization check
        if ($report->organization_id !== $request->user()->organization_id) {
            abort(403, 'Unauthorized access to this report');
        }

        $html = $this->generatePDFHTML($report);

        // Return HTML that can be converted to PDF on frontend
        // Or use a PDF library like dompdf, mpdf, or snappy
        return response($html, 200)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="rapport-depenses-' . $report->reference . '.html"');
    }

    /**
     * Export expense report as Excel (CSV format)
     */
    public function exportExcel(Request $request, $id)
    {
        $report = ExpenseReport::with([
            'user',
            'organization',
            'items.category',
            'mileageExpenses.vehicle'
        ])->findOrFail($id);

        // Authorization check
        if ($report->organization_id !== $request->user()->organization_id) {
            abort(403, 'Unauthorized access to this report');
        }

        $csv = $this->generateCSV($report);

        return response($csv, 200)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="rapport-depenses-' . $report->reference . '.csv"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Export multiple reports as Excel
     */
    public function exportMultipleExcel(Request $request)
    {
        $request->validate([
            'report_ids' => 'required|array',
            'report_ids.*' => 'exists:expense_reports,id',
        ]);

        $reports = ExpenseReport::with([
            'user',
            'organization',
            'items.category',
            'mileageExpenses.vehicle'
        ])->whereIn('id', $request->report_ids)
            ->where('organization_id', $request->user()->organization_id)
            ->get();

        $csv = $this->generateMultipleReportsCSV($reports);

        return response($csv, 200)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="rapports-depenses-' . date('Y-m-d') . '.csv"');
    }

    /**
     * Generate PDF HTML
     */
    private function generatePDFHTML(ExpenseReport $report): string
    {
        $items = $report->items;
        $mileageExpenses = $report->mileageExpenses;

        $html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de Dépenses - {$report->reference}</title>
    <style>
        @page {
            margin: 2cm;
            size: A4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
        }

        .header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #667eea;
            font-size: 24pt;
            margin-bottom: 10px;
        }

        .header .info {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .info-block {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            flex: 1;
            margin-right: 15px;
        }

        .info-block:last-child {
            margin-right: 0;
        }

        .info-block h3 {
            font-size: 10pt;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-block p {
            font-size: 11pt;
            margin: 4px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-draft { background: #e9ecef; color: #495057; }
        .status-submitted { background: #fff3cd; color: #856404; }
        .status-approved { background: #d1e7dd; color: #0f5132; }
        .status-rejected { background: #f8d7da; color: #842029; }
        .status-paid { background: #cfe2ff; color: #084298; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
        }

        table thead {
            background: #667eea;
            color: white;
        }

        table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 10pt;
        }

        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 10pt;
        }

        table tbody tr:hover {
            background: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .summary-row:last-child {
            border-bottom: none;
            font-size: 14pt;
            font-weight: bold;
            color: #667eea;
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px solid #667eea;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 9pt;
            color: #666;
            text-align: center;
        }

        .section-title {
            font-size: 14pt;
            color: #667eea;
            margin: 30px 0 15px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #667eea;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$report->organization->name}</h1>
        <p>{$report->organization->address}, {$report->organization->city} {$report->organization->postal_code}</p>
        <p>Matricule Fiscal: {$report->organization->matricule_fiscal}</p>
        <p>Tél: {$report->organization->phone} | Email: {$report->organization->email}</p>

        <div class="info" style="margin-top: 20px; display: table; width: 100%;">
            <div class="info-block" style="display: table-cell; padding: 10px; background: #f8f9fa;">
                <h3>Référence</h3>
                <p><strong>{$report->reference}</strong></p>
                <p style="margin-top: 5px;"><span class="status-badge status-{$report->status}">{$this->translateStatus($report->status)}</span></p>
            </div>
            <div class="info-block" style="display: table-cell; padding: 10px; background: #f8f9fa;">
                <h3>Employé</h3>
                <p><strong>{$report->user->name}</strong></p>
                <p>{$report->user->job_title}</p>
                <p>{$report->user->department}</p>
            </div>
            <div class="info-block" style="display: table-cell; padding: 10px; background: #f8f9fa;">
                <h3>Dates</h3>
                <p>Créé: {$report->created_at->format('d/m/Y')}</p>
HTML;

        if ($report->submitted_at) {
            $html .= "<p>Soumis: {$report->submitted_at->format('d/m/Y')}</p>";
        }
        if ($report->approved_at) {
            $html .= "<p>Approuvé: {$report->approved_at->format('d/m/Y')}</p>";
        }

        $html .= <<<HTML
            </div>
        </div>
    </div>

    <h2 style="font-size: 16pt; margin: 20px 0 10px 0;">{$report->title}</h2>
    <p style="color: #666; margin-bottom: 20px;">{$report->description}</p>
HTML;

        // Items table
        if ($items->count() > 0) {
            $html .= <<<HTML

    <h3 class="section-title">Dépenses</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Catégorie</th>
                <th>Description</th>
                <th>Fournisseur</th>
                <th class="text-right">HT</th>
                <th class="text-right">TVA</th>
                <th class="text-right">TTC</th>
            </tr>
        </thead>
        <tbody>
HTML;

            foreach ($items as $item) {
                $html .= <<<HTML
            <tr>
                <td>{$item->date->format('d/m/Y')}</td>
                <td>{$item->category->name}</td>
                <td>{$item->description}</td>
                <td>{$item->merchant_name}</td>
                <td class="text-right">{$this->formatAmount($item->amount_ht)} TND</td>
                <td class="text-right">{$this->formatAmount($item->tva_amount)} TND</td>
                <td class="text-right"><strong>{$this->formatAmount($item->amount)} TND</strong></td>
            </tr>
HTML;
            }

            $html .= <<<HTML
        </tbody>
    </table>
HTML;
        }

        // Mileage expenses table
        if ($mileageExpenses->count() > 0) {
            $html .= <<<HTML

    <h3 class="section-title">Frais Kilométriques</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Départ</th>
                <th>Arrivée</th>
                <th>Véhicule</th>
                <th class="text-right">Distance</th>
                <th class="text-right">Tarif/km</th>
                <th class="text-right">Montant</th>
            </tr>
        </thead>
        <tbody>
HTML;

            foreach ($mileageExpenses as $mileage) {
                $distance = $mileage->is_round_trip ? $mileage->total_distance_km : $mileage->distance_km;
                $html .= <<<HTML
            <tr>
                <td>{$mileage->date->format('d/m/Y')}</td>
                <td>{$mileage->departure}</td>
                <td>{$mileage->arrival}</td>
                <td>{$mileage->vehicle->name}</td>
                <td class="text-right">{$distance} km</td>
                <td class="text-right">{$this->formatAmount($mileage->rate_per_km)} TND</td>
                <td class="text-right"><strong>{$this->formatAmount($mileage->total_amount)} TND</strong></td>
            </tr>
HTML;
            }

            $html .= <<<HTML
        </tbody>
    </table>
HTML;
        }

        // Summary
        $totalItems = $items->sum('amount');
        $totalMileage = $mileageExpenses->sum('total_amount');
        $grandTotal = $totalItems + $totalMileage;

        $html .= <<<HTML

    <div class="summary">
        <div class="summary-row">
            <span>Total Dépenses:</span>
            <span><strong>{$this->formatAmount($totalItems)} TND</strong></span>
        </div>
        <div class="summary-row">
            <span>Total Frais Kilométriques:</span>
            <span><strong>{$this->formatAmount($totalMileage)} TND</strong></span>
        </div>
        <div class="summary-row">
            <span>TOTAL GÉNÉRAL:</span>
            <span>{$this->formatAmount($grandTotal)} TND</span>
        </div>
    </div>
HTML;

        // Approval section
        if ($report->approved_at && $report->approver) {
            $html .= <<<HTML

    <div style="margin-top: 40px; padding: 15px; background: #d1e7dd; border-left: 4px solid #0f5132;">
        <p><strong>Approuvé par:</strong> {$report->approver->name}</p>
        <p><strong>Date d'approbation:</strong> {$report->approved_at->format('d/m/Y à H:i')}</p>
    </div>
HTML;
        }

        if ($report->rejected_at && $report->rejecter) {
            $html .= <<<HTML

    <div style="margin-top: 40px; padding: 15px; background: #f8d7da; border-left: 4px solid #842029;">
        <p><strong>Rejeté par:</strong> {$report->rejecter->name}</p>
        <p><strong>Date de rejet:</strong> {$report->rejected_at->format('d/m/Y à H:i')}</p>
        <p><strong>Raison:</strong> {$report->rejection_reason}</p>
    </div>
HTML;
        }

        $html .= <<<HTML

    <div class="footer">
        <p>Document généré le {$this->formatDate(now())} par Compteo TN</p>
        <p>Système de gestion des notes de frais - compteo.tn</p>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    /**
     * Generate CSV
     */
    private function generateCSV(ExpenseReport $report): string
    {
        $output = fopen('php://temp', 'r+');

        // UTF-8 BOM for Excel compatibility
        fwrite($output, "\xEF\xBB\xBF");

        // Header
        fputcsv($output, ['Rapport de Dépenses - ' . $report->reference], ';');
        fputcsv($output, ['Organisation', $report->organization->name], ';');
        fputcsv($output, ['Employé', $report->user->name], ';');
        fputcsv($output, ['Statut', $this->translateStatus($report->status)], ';');
        fputcsv($output, [], ';');

        // Items
        if ($report->items->count() > 0) {
            fputcsv($output, ['DÉPENSES'], ';');
            fputcsv($output, ['Date', 'Catégorie', 'Description', 'Fournisseur', 'HT', 'TVA', 'TTC', 'Devise'], ';');

            foreach ($report->items as $item) {
                fputcsv($output, [
                    $item->date->format('d/m/Y'),
                    $item->category->name,
                    $item->description,
                    $item->merchant_name,
                    $this->formatAmount($item->amount_ht),
                    $this->formatAmount($item->tva_amount),
                    $this->formatAmount($item->amount),
                    $item->currency,
                ], ';');
            }

            fputcsv($output, [], ';');
        }

        // Mileage
        if ($report->mileageExpenses->count() > 0) {
            fputcsv($output, ['FRAIS KILOMÉTRIQUES'], ';');
            fputcsv($output, ['Date', 'Départ', 'Arrivée', 'Véhicule', 'Distance (km)', 'Tarif/km', 'Montant', 'Devise'], ';');

            foreach ($report->mileageExpenses as $mileage) {
                $distance = $mileage->is_round_trip ? $mileage->total_distance_km : $mileage->distance_km;
                fputcsv($output, [
                    $mileage->date->format('d/m/Y'),
                    $mileage->departure,
                    $mileage->arrival,
                    $mileage->vehicle->name,
                    $distance,
                    $this->formatAmount($mileage->rate_per_km),
                    $this->formatAmount($mileage->total_amount),
                    $mileage->currency,
                ], ';');
            }

            fputcsv($output, [], ';');
        }

        // Summary
        fputcsv($output, ['RÉSUMÉ'], ';');
        fputcsv($output, ['Total Dépenses', $this->formatAmount($report->items->sum('amount')) . ' TND'], ';');
        fputcsv($output, ['Total Frais Kilométriques', $this->formatAmount($report->mileageExpenses->sum('total_amount')) . ' TND'], ';');
        fputcsv($output, ['TOTAL GÉNÉRAL', $this->formatAmount($report->total_amount) . ' TND'], ';');

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Generate CSV for multiple reports
     */
    private function generateMultipleReportsCSV($reports): string
    {
        $output = fopen('php://temp', 'r+');

        // UTF-8 BOM
        fwrite($output, "\xEF\xBB\xBF");

        // Header
        fputcsv($output, ['Référence', 'Titre', 'Employé', 'Département', 'Statut', 'Date Création', 'Date Soumission', 'Date Approbation', 'Montant Total', 'Devise'], ';');

        foreach ($reports as $report) {
            fputcsv($output, [
                $report->reference,
                $report->title,
                $report->user->name,
                $report->user->department,
                $this->translateStatus($report->status),
                $report->created_at->format('d/m/Y'),
                $report->submitted_at ? $report->submitted_at->format('d/m/Y') : '',
                $report->approved_at ? $report->approved_at->format('d/m/Y') : '',
                $this->formatAmount($report->total_amount),
                $report->currency,
            ], ';');
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Format amount with 3 decimals
     */
    private function formatAmount($amount): string
    {
        return number_format((float) $amount, 3, '.', '');
    }

    /**
     * Format date
     */
    private function formatDate($date): string
    {
        return $date->format('d/m/Y à H:i');
    }

    /**
     * Translate status to French
     */
    private function translateStatus($status): string
    {
        return match ($status) {
            'draft' => 'Brouillon',
            'submitted' => 'Soumis',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            'paid' => 'Payé',
            default => $status,
        };
    }
}
