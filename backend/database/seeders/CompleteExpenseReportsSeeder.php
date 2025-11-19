<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\ExpenseItem;
use App\Models\ExpenseReport;
use App\Models\MileageExpense;
use App\Models\Organization;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class CompleteExpenseReportsSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::first();
        if (!$organization) {
            $this->command->error('No organization found. Please run DemoDataSeeder first.');
            return;
        }

        $employee = $organization->users()->where('role', 'employee')->first();
        $manager = $organization->users()->where('role', 'manager')->first();
        $daf = $organization->users()->where('role', 'daf')->first();

        $categories = ExpenseCategory::all()->keyBy('code');
        $vehicle = Vehicle::where('user_id', $employee->id)->first();

        // 1. Rapport approuvé (mois dernier)
        $report1 = ExpenseReport::create([
            'organization_id' => $organization->id,
            'user_id' => $employee->id,
            'title' => 'Déplacement client Sfax',
            'description' => 'Visite client important à Sfax avec réunion et repas d\'affaires',
            'status' => 'approved',
            'currency' => 'TND',
            'submitted_at' => now()->subDays(25),
            'approved_at' => now()->subDays(23),
            'approved_by' => $daf->id,
        ]);

        // Dépenses du rapport 1
        ExpenseItem::create([
            'expense_report_id' => $report1->id,
            'expense_category_id' => $categories['FUEL']->id,
            'date' => now()->subDays(26),
            'description' => 'Plein d\'essence station Total',
            'merchant_name' => 'Total Energies Tunis',
            'amount' => 85.500,
            'amount_ht' => 71.849,
            'tva_rate' => 19.00,
            'tva_amount' => 13.651,
            'currency' => 'TND',
        ]);

        ExpenseItem::create([
            'expense_report_id' => $report1->id,
            'expense_category_id' => $categories['RESTAURANT']->id,
            'date' => now()->subDays(26),
            'description' => 'Déjeuner d\'affaires avec client',
            'merchant_name' => 'Restaurant Le Corail Sfax',
            'merchant_vat_number' => '1234567/A/M/000',
            'amount' => 120.000,
            'amount_ht' => 100.840,
            'tva_rate' => 19.00,
            'tva_amount' => 19.160,
            'currency' => 'TND',
            'guest_count' => 2,
            'guest_names' => ['Ahmed Ben Ali', 'Directeur commercial'],
        ]);

        ExpenseItem::create([
            'expense_report_id' => $report1->id,
            'expense_category_id' => $categories['PARKING']->id,
            'date' => now()->subDays(26),
            'description' => 'Parking centre ville Sfax',
            'merchant_name' => 'Parking Municipal',
            'amount' => 5.000,
            'amount_ht' => 4.202,
            'tva_rate' => 19.00,
            'tva_amount' => 0.798,
            'currency' => 'TND',
        ]);

        // Frais kilométriques rapport 1
        if ($vehicle) {
            MileageExpense::create([
                'expense_report_id' => $report1->id,
                'organization_id' => $organization->id,
                'vehicle_id' => $vehicle->id,
                'date' => now()->subDays(26),
                'departure' => 'Tunis Bureau',
                'arrival' => 'Sfax Centre Ville',
                'distance_km' => 270,
                'is_round_trip' => true,
                'total_distance_km' => 540,
                'rate_per_km' => 0.350, // 5 CV essence
                'total_amount' => 189.000,
                'purpose' => 'Visite client et signature contrat',
                'currency' => 'TND',
            ]);
        }

        $report1->calculateTotals();

        // 2. Rapport soumis (en attente d'approbation)
        $report2 = ExpenseReport::create([
            'organization_id' => $organization->id,
            'user_id' => $employee->id,
            'title' => 'Formation Laravel Tunis',
            'description' => 'Formation professionnelle Laravel + Vue.js sur 2 jours',
            'status' => 'submitted',
            'currency' => 'TND',
            'submitted_at' => now()->subDays(3),
        ]);

        ExpenseItem::create([
            'expense_report_id' => $report2->id,
            'expense_category_id' => $categories['TRAINING']->id,
            'date' => now()->subDays(5),
            'description' => 'Inscription formation Laravel avancé',
            'merchant_name' => 'GoMyCode Tunis',
            'merchant_vat_number' => '9876543/B/A/000',
            'amount' => 800.000,
            'amount_ht' => 672.269,
            'tva_rate' => 19.00,
            'tva_amount' => 127.731,
            'currency' => 'TND',
        ]);

        ExpenseItem::create([
            'expense_report_id' => $report2->id,
            'expense_category_id' => $categories['RESTAURANT']->id,
            'date' => now()->subDays(5),
            'description' => 'Déjeuner pendant la formation',
            'merchant_name' => 'Café de Paris',
            'amount' => 25.000,
            'amount_ht' => 21.008,
            'tva_rate' => 19.00,
            'tva_amount' => 3.992,
            'currency' => 'TND',
        ]);

        ExpenseItem::create([
            'expense_report_id' => $report2->id,
            'expense_category_id' => $categories['RESTAURANT']->id,
            'date' => now()->subDays(4),
            'description' => 'Déjeuner jour 2 formation',
            'merchant_name' => 'Café de Paris',
            'amount' => 25.000,
            'amount_ht' => 21.008,
            'tva_rate' => 19.00,
            'tva_amount' => 3.992,
            'currency' => 'TND',
        ]);

        ExpenseItem::create([
            'expense_report_id' => $report2->id,
            'expense_category_id' => $categories['SUPPLIES']->id,
            'date' => now()->subDays(5),
            'description' => 'Achat livre "Laravel Up & Running"',
            'merchant_name' => 'Librairie Clairefontaine',
            'amount' => 95.000,
            'amount_ht' => 79.832,
            'tva_rate' => 19.00,
            'tva_amount' => 15.168,
            'currency' => 'TND',
        ]);

        $report2->calculateTotals();

        // 3. Rapport brouillon (en cours de saisie)
        $report3 = ExpenseReport::create([
            'organization_id' => $organization->id,
            'user_id' => $employee->id,
            'title' => 'Frais Novembre 2025',
            'description' => 'Frais divers du mois en cours',
            'status' => 'draft',
            'currency' => 'TND',
        ]);

        ExpenseItem::create([
            'expense_report_id' => $report3->id,
            'expense_category_id' => $categories['PHONE']->id,
            'date' => now()->subDays(2),
            'description' => 'Recharge téléphone professionnel',
            'merchant_name' => 'Ooredoo',
            'amount' => 20.000,
            'amount_ht' => 16.807,
            'tva_rate' => 19.00,
            'tva_amount' => 3.193,
            'currency' => 'TND',
        ]);

        ExpenseItem::create([
            'expense_report_id' => $report3->id,
            'expense_category_id' => $categories['PARKING']->id,
            'date' => now()->subDay(),
            'description' => 'Parking rendez-vous client',
            'merchant_name' => 'Parking Tunisia Mall',
            'amount' => 8.000,
            'amount_ht' => 6.723,
            'tva_rate' => 19.00,
            'tva_amount' => 1.277,
            'currency' => 'TND',
        ]);

        $report3->calculateTotals();

        // 4. Rapport rejeté
        $report4 = ExpenseReport::create([
            'organization_id' => $organization->id,
            'user_id' => $employee->id,
            'title' => 'Frais Octobre 2025',
            'description' => 'Frais du mois d\'octobre',
            'status' => 'rejected',
            'currency' => 'TND',
            'submitted_at' => now()->subDays(15),
            'rejected_at' => now()->subDays(14),
            'rejected_by' => $manager->id,
            'rejection_reason' => 'Justificatifs manquants pour les frais de restaurant. Merci de soumettre les factures originales.',
        ]);

        ExpenseItem::create([
            'expense_report_id' => $report4->id,
            'expense_category_id' => $categories['RESTAURANT']->id,
            'date' => now()->subDays(20),
            'description' => 'Repas client',
            'merchant_name' => 'Restaurant inconnu',
            'amount' => 150.000,
            'amount_ht' => 126.050,
            'tva_rate' => 19.00,
            'tva_amount' => 23.950,
            'currency' => 'TND',
        ]);

        $report4->calculateTotals();

        // 5. Rapport manager (soumis)
        $report5 = ExpenseReport::create([
            'organization_id' => $organization->id,
            'user_id' => $manager->id,
            'title' => 'Déplacement Sousse - Prospection',
            'description' => 'Prospection commerciale région Sousse',
            'status' => 'submitted',
            'currency' => 'TND',
            'submitted_at' => now()->subDays(2),
        ]);

        ExpenseItem::create([
            'expense_report_id' => $report5->id,
            'expense_category_id' => $categories['HOTEL']->id,
            'date' => now()->subDays(4),
            'description' => 'Hôtel Sousse - 1 nuit',
            'merchant_name' => 'Hôtel Marhaba Sousse',
            'merchant_vat_number' => '5555555/C/M/000',
            'amount' => 180.000,
            'amount_ht' => 151.261,
            'tva_rate' => 19.00,
            'tva_amount' => 28.739,
            'currency' => 'TND',
        ]);

        ExpenseItem::create([
            'expense_report_id' => $report5->id,
            'expense_category_id' => $categories['RESTAURANT']->id,
            'date' => now()->subDays(4),
            'description' => 'Dîner prospects',
            'merchant_name' => 'Restaurant La Marina',
            'amount' => 220.000,
            'amount_ht' => 184.874,
            'tva_rate' => 19.00,
            'tva_amount' => 35.126,
            'currency' => 'TND',
            'guest_count' => 3,
            'guest_names' => ['Client 1', 'Client 2', 'Manager'],
        ]);

        ExpenseItem::create([
            'expense_report_id' => $report5->id,
            'expense_category_id' => $categories['FUEL']->id,
            'date' => now()->subDays(4),
            'description' => 'Carburant trajet Sousse',
            'merchant_name' => 'Total',
            'amount' => 75.000,
            'amount_ht' => 63.025,
            'tva_rate' => 19.00,
            'tva_amount' => 11.975,
            'currency' => 'TND',
        ]);

        $report5->calculateTotals();

        // 6. Rapport payé (ancien)
        $report6 = ExpenseReport::create([
            'organization_id' => $organization->id,
            'user_id' => $employee->id,
            'title' => 'Frais Septembre 2025',
            'description' => 'Frais mensuels septembre',
            'status' => 'paid',
            'currency' => 'TND',
            'submitted_at' => now()->subDays(45),
            'approved_at' => now()->subDays(42),
            'approved_by' => $daf->id,
            'paid_at' => now()->subDays(35),
        ]);

        ExpenseItem::create([
            'expense_report_id' => $report6->id,
            'expense_category_id' => $categories['INTERNET']->id,
            'date' => now()->subDays(50),
            'description' => 'Abonnement internet bureau',
            'merchant_name' => 'Topnet',
            'amount' => 49.000,
            'amount_ht' => 41.177,
            'tva_rate' => 19.00,
            'tva_amount' => 7.823,
            'currency' => 'TND',
        ]);

        ExpenseItem::create([
            'expense_report_id' => $report6->id,
            'expense_category_id' => $categories['SUPPLIES']->id,
            'date' => now()->subDays(48),
            'description' => 'Fournitures bureau',
            'merchant_name' => 'Papeterie Moderne',
            'amount' => 65.000,
            'amount_ht' => 54.622,
            'tva_rate' => 19.00,
            'tva_amount' => 10.378,
            'currency' => 'TND',
        ]);

        $report6->calculateTotals();

        $this->command->info('✅ Created 6 complete expense reports with various statuses');
        $this->command->info('   - 1 Approved report (with mileage)');
        $this->command->info('   - 2 Submitted reports (pending approval)');
        $this->command->info('   - 1 Draft report');
        $this->command->info('   - 1 Rejected report');
        $this->command->info('   - 1 Paid report');
    }
}
