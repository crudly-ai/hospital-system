<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patients = [
            ['name' => 'Sample Patient 1', 'dob' => '2026-01-23', 'phone' => 'Sample phone 1', 'email' => 'Sample email 1', 'address' => 'Sample description for Patient 1', 'status' => 'active'],
            ['name' => 'Sample Patient 2', 'dob' => '2026-01-24', 'phone' => 'Sample phone 2', 'email' => 'Sample email 2', 'address' => 'Sample description for Patient 2', 'status' => 'active'],
            ['name' => 'Sample Patient 3', 'dob' => '2026-01-25', 'phone' => 'Sample phone 3', 'email' => 'Sample email 3', 'address' => 'Sample description for Patient 3', 'status' => 'active'],

        ];
        
        foreach ($patients as $patient) {
            Patient::firstOrCreate(['name' => $patient['name']], array_merge($patient, ['created_by' => 1]));
        }
    }
}