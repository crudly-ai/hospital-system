<?php

namespace Database\Factories;

use App\Models\Admission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdmissionFactory extends Factory
{
    protected $model = Admission::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'admitted_on' => $this->faker->date(),
            'discharged_on' => $this->faker->date(),
            'room_number' => $this->faker->words(3, true),
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