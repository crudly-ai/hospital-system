<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            AdminUserSeeder::class,
            DefaultMediaSeeder::class,
            SystemSettingsTableSeeder::class,
            
                    DepartmentSeeder::class,
                    DoctorSeeder::class,
                    PatientSeeder::class,
                    TreatmentSeeder::class,
                    AppointmentSeeder::class,
                    AdmissionSeeder::class,
                    BillingSeeder::class,
        ]);
    }
}
