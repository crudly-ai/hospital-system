<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = [
            ['name' => 'Sample Doctor 1', 'specialty' => 'Sample specialty 1', 'department_id' => 1, 'phone' => 'Sample phone 1', 'email' => 'Sample email 1', 'status' => 'active'],
            ['name' => 'Sample Doctor 2', 'specialty' => 'Sample specialty 2', 'department_id' => 2, 'phone' => 'Sample phone 2', 'email' => 'Sample email 2', 'status' => 'active'],
            ['name' => 'Sample Doctor 3', 'specialty' => 'Sample specialty 3', 'department_id' => 3, 'phone' => 'Sample phone 3', 'email' => 'Sample email 3', 'status' => 'active'],

        ];
        
        foreach ($doctors as $doctor) {
            Doctor::firstOrCreate(['name' => $doctor['name']], array_merge($doctor, ['created_by' => 1]));
        }
    }
}