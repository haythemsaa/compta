<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Transport',
                'code' => 'TRANSPORT',
                'icon' => '🚗',
                'color' => '#3B82F6',
                'description' => 'Frais de transport (taxi, bus, train, avion)',
                'accounting_code' => '6251',
                'requires_justification' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Restaurant',
                'code' => 'RESTAURANT',
                'icon' => '🍽️',
                'color' => '#EF4444',
                'description' => 'Repas d\'affaires et restauration',
                'accounting_code' => '6253',
                'requires_justification' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Hôtel',
                'code' => 'HOTEL',
                'icon' => '🏨',
                'color' => '#8B5CF6',
                'description' => 'Hébergement professionnel',
                'accounting_code' => '6251',
                'requires_justification' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Carburant',
                'code' => 'FUEL',
                'icon' => '⛽',
                'color' => '#F59E0B',
                'description' => 'Achats de carburant',
                'accounting_code' => '6252',
                'requires_justification' => true,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Parking',
                'code' => 'PARKING',
                'icon' => '🅿️',
                'color' => '#6B7280',
                'description' => 'Frais de stationnement',
                'accounting_code' => '6251',
                'requires_justification' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Fournitures',
                'code' => 'SUPPLIES',
                'icon' => '📦',
                'color' => '#10B981',
                'description' => 'Fournitures de bureau',
                'accounting_code' => '606',
                'requires_justification' => true,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Téléphone',
                'code' => 'PHONE',
                'icon' => '📱',
                'color' => '#06B6D4',
                'description' => 'Frais téléphoniques',
                'accounting_code' => '626',
                'requires_justification' => true,
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Internet',
                'code' => 'INTERNET',
                'icon' => '🌐',
                'color' => '#14B8A6',
                'description' => 'Frais internet et télécommunications',
                'accounting_code' => '626',
                'requires_justification' => true,
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Formation',
                'code' => 'TRAINING',
                'icon' => '📚',
                'color' => '#EC4899',
                'description' => 'Frais de formation professionnelle',
                'accounting_code' => '617',
                'requires_justification' => true,
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Autre',
                'code' => 'OTHER',
                'icon' => '📋',
                'color' => '#64748B',
                'description' => 'Autres frais professionnels',
                'accounting_code' => '625',
                'requires_justification' => true,
                'is_active' => true,
                'sort_order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::create($category);
        }
    }
}
