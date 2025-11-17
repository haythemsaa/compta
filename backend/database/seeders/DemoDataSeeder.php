<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo organization
        $organization = Organization::create([
            'name' => 'Compteo Demo',
            'legal_name' => 'Compteo Tunisia SARL',
            'matricule_fiscal' => '1234567/A/M/000',
            'email' => 'contact@compteo.tn',
            'phone' => '+216 71 123 456',
            'address' => 'Avenue Habib Bourguiba',
            'city' => 'Tunis',
            'postal_code' => '1000',
            'country' => 'TN',
            'currency' => 'TND',
            'default_tva_rate' => 19.00,
            'plan' => 'business',
            'is_active' => true,
            'subscribed_at' => now(),
        ]);

        // Create demo users with different roles
        $users = [
            [
                'name' => 'Admin Demo',
                'email' => 'admin@compteo.tn',
                'role' => 'admin',
                'job_title' => 'Administrateur',
                'department' => 'IT',
            ],
            [
                'name' => 'DAF Demo',
                'email' => 'daf@compteo.tn',
                'role' => 'daf',
                'job_title' => 'Directeur Administratif et Financier',
                'department' => 'Finance',
            ],
            [
                'name' => 'Comptable Demo',
                'email' => 'comptable@compteo.tn',
                'role' => 'accountant',
                'job_title' => 'Comptable',
                'department' => 'Comptabilité',
            ],
            [
                'name' => 'Manager Demo',
                'email' => 'manager@compteo.tn',
                'role' => 'manager',
                'job_title' => 'Manager',
                'department' => 'Commercial',
            ],
            [
                'name' => 'Employé Demo',
                'email' => 'demo@compteo.tn',
                'role' => 'employee',
                'job_title' => 'Commercial',
                'department' => 'Ventes',
            ],
        ];

        foreach ($users as $userData) {
            User::create(array_merge($userData, [
                'organization_id' => $organization->id,
                'password' => Hash::make('demo'),
                'phone' => '+216 20 000 000',
                'is_active' => true,
            ]));
        }

        // Create demo vehicles
        $employee = $organization->users()->where('role', 'employee')->first();

        Vehicle::create([
            'organization_id' => $organization->id,
            'user_id' => $employee->id,
            'name' => 'Renault Clio - 123 TU 1234',
            'brand' => 'Renault',
            'model' => 'Clio',
            'registration_number' => '123 TU 1234',
            'fiscal_power' => 5,
            'fuel_type' => 'essence',
            'type' => 'personal',
            'is_active' => true,
        ]);

        Vehicle::create([
            'organization_id' => $organization->id,
            'user_id' => null,
            'name' => 'Peugeot 208 - 456 TU 5678',
            'brand' => 'Peugeot',
            'model' => '208',
            'registration_number' => '456 TU 5678',
            'fiscal_power' => 6,
            'fuel_type' => 'diesel',
            'type' => 'company',
            'is_active' => true,
        ]);
    }
}
