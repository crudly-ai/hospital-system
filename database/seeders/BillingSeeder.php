<?php

namespace Database\Seeders;

use App\Models\Billing;
use Illuminate\Database\Seeder;

class BillingSeeder extends Seeder
{
    public function run(): void
    {
        $billings = [
            ['patient_id' => '1', 'amount' => 25.99, 'payment_date' => '2026-01-23', 'status' => 'active'],
            ['patient_id' => '2', 'amount' => 51.98, 'payment_date' => '2026-01-24', 'status' => 'active'],
            ['patient_id' => '3', 'amount' => 77.97, 'payment_date' => '2026-01-25', 'status' => 'active'],

        ];
        
        foreach ($billings as $billing) {
            Billing::firstOrCreate(['patient_id' => $billing['patient_id']], array_merge($billing, ['created_by' => 1]));
        }
    }
}