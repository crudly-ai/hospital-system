<?php

namespace Tests\Unit;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_appointment_can_be_created()
    {
        $user = User::factory()->create();
        
        $appointment = Appointment::create([
            'date' => '2024-01-01',
            'time' => '09:00',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('appointments', [
            'created_by' => $user->id,
        ]);
    }

    public function test_appointment_has_fillable_attributes()
    {
        $appointment = new Appointment();
        
        $this->assertContains('doctor_id', $appointment->getFillable());
        $this->assertContains('patient_id', $appointment->getFillable());
        $this->assertContains('date', $appointment->getFillable());
        $this->assertContains('time', $appointment->getFillable());
        $this->assertContains('status', $appointment->getFillable());
        $this->assertContains('created_by', $appointment->getFillable());
    }

    public function test_appointment_has_proper_casts()
    {
        $appointment = new Appointment();
        $casts = $appointment->getCasts();
        
        $this->assertEquals('date', $casts['date']);
        $this->assertEquals('string', $casts['status']);
    }

    public function test_appointment_belongs_to_doctor()
    {
        $doctor = Doctor::factory()->create();
        $appointment = Appointment::factory()->create(['doctor_id' => $doctor->id]);
        
        $this->assertInstanceOf(Doctor::class, $appointment->doctor);
        $this->assertEquals($doctor->id, $appointment->doctor->id);
    }

    public function test_appointment_belongs_to_patient()
    {
        $patient = Patient::factory()->create();
        $appointment = Appointment::factory()->create(['patient_id' => $patient->id]);
        
        $this->assertInstanceOf(Patient::class, $appointment->patient);
        $this->assertEquals($patient->id, $appointment->patient->id);
    }

    public function test_appointment_belongs_to_creator()
    {
        $user = User::factory()->create();
        $appointment = Appointment::factory()->create(['created_by' => $user->id]);
        
        $this->assertInstanceOf(User::class, $appointment->creator);
        $this->assertEquals($user->id, $appointment->creator->id);
    }

    public function test_appointment_factory_creates_valid_appointment()
    {
        $appointment = Appointment::factory()->create();

        $this->assertNotNull($appointment->created_by);
    }

    public function test_appointment_factory_active_state()
    {
        $appointment = Appointment::factory()->active()->create();

        $this->assertEquals('active', $appointment->status);
    }

    public function test_appointment_factory_inactive_state()
    {
        $appointment = Appointment::factory()->inactive()->create();

        $this->assertEquals('inactive', $appointment->status);
    }
}