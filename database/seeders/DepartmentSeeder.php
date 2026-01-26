<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Sample Department 1', 'description' => 'Sample description for Department 1', 'status' => 'active'],
            ['name' => 'Sample Department 2', 'description' => 'Sample description for Department 2', 'status' => 'active'],
            ['name' => 'Sample Department 3', 'description' => 'Sample description for Department 3', 'status' => 'active'],

        ];
        
        foreach ($departments as $department) {
            Department::firstOrCreate(['name' => $department['name']], array_merge($department, ['created_by' => 1]));
        }
    }
}