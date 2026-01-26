<?php

namespace Database\Seeders;

use App\Models\Admission;
use Illuminate\Database\Seeder;

class AdmissionSeeder extends Seeder
{
    public function run(): void
    {
        $admissions = [
            ['patient_id' => '1', 'admitted_on' => '2026-01-23', 'discharged_on' => '2026-01-23', 'room_number' => 'Sample room_number 1', 'status' => 'active'],
            ['patient_id' => '2', 'admitted_on' => '2026-01-24', 'discharged_on' => '2026-01-24', 'room_number' => 'Sample room_number 2', 'status' => 'active'],
            ['patient_id' => '3', 'admitted_on' => '2026-01-25', 'discharged_on' => '2026-01-25', 'room_number' => 'Sample room_number 3', 'status' => 'active'],

        ];
        
        foreach ($admissions as $admission) {
            Admission::firstOrCreate(['patient_id' => $admission['patient_id']], array_merge($admission, ['created_by' => 1]));
        }
    }
}