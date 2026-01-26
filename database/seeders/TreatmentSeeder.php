<?php

namespace Database\Seeders;

use App\Models\Treatment;
use Illuminate\Database\Seeder;

class TreatmentSeeder extends Seeder
{
    public function run(): void
    {
        $treatments = [
            ['name' => 'Sample Treatment 1', 'description' => 'Sample description for Treatment 1', 'cost' => 25.99, 'status' => 'active'],
            ['name' => 'Sample Treatment 2', 'description' => 'Sample description for Treatment 2', 'cost' => 51.98, 'status' => 'active'],
            ['name' => 'Sample Treatment 3', 'description' => 'Sample description for Treatment 3', 'cost' => 77.97, 'status' => 'active'],

        ];
        
        foreach ($treatments as $treatment) {
            Treatment::firstOrCreate(['name' => $treatment['name']], array_merge($treatment, ['created_by' => 1]));
        }
    }
}