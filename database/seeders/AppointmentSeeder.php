<?php

namespace Database\Seeders;

use App\Models\Appointment;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $appointments = [
            ['doctor_id' => '1', 'patient_id' => 1, 'date' => '2026-01-23', 'time' => '10:00', 'status' => 'active'],
            ['doctor_id' => '2', 'patient_id' => 2, 'date' => '2026-01-24', 'time' => '11:00', 'status' => 'active'],
            ['doctor_id' => '3', 'patient_id' => 3, 'date' => '2026-01-25', 'time' => '12:00', 'status' => 'active'],

        ];
        
        foreach ($appointments as $appointment) {
            Appointment::firstOrCreate(['doctor_id' => $appointment['doctor_id']], array_merge($appointment, ['created_by' => 1]));
        }
    }
}