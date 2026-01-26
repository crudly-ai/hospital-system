<?php

namespace Database\Factories;

use App\Models\Billing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BillingFactory extends Factory
{
    protected $model = Billing::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'payment_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'created_by' => User::factory(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}