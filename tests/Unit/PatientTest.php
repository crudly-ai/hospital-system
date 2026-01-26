<?php

namespace Tests\Unit;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_be_created()
    {
        $user = User::factory()->create();
        
        $patient = Patient::create([
            'name' => 'Test Name',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('patients', [
            'name' => 'Test Name',
            'created_by' => $user->id,
        ]);
    }

    public function test_patient_has_fillable_attributes()
    {
        $patient = new Patient();
        
        $this->assertContains('name', $patient->getFillable());
        $this->assertContains('dob', $patient->getFillable());
        $this->assertContains('phone', $patient->getFillable());
        $this->assertContains('email', $patient->getFillable());
        $this->assertContains('address', $patient->getFillable());
        $this->assertContains('status', $patient->getFillable());
        $this->assertContains('created_by', $patient->getFillable());
    }

    public function test_patient_has_proper_casts()
    {
        $patient = new Patient();
        $casts = $patient->getCasts();
        
        $this->assertEquals('date', $casts['dob']);
        $this->assertEquals('string', $casts['status']);
    }

    public function test_patient_belongs_to_creator()
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create(['created_by' => $user->id]);
        
        $this->assertInstanceOf(User::class, $patient->creator);
        $this->assertEquals($user->id, $patient->creator->id);
    }

    public function test_patient_factory_creates_valid_patient()
    {
        $patient = Patient::factory()->create();

        $this->assertNotNull($patient->name);
        $this->assertNotNull($patient->created_by);
    }

    public function test_patient_factory_active_state()
    {
        $patient = Patient::factory()->active()->create();

        $this->assertEquals('active', $patient->status);
    }

    public function test_patient_factory_inactive_state()
    {
        $patient = Patient::factory()->inactive()->create();

        $this->assertEquals('inactive', $patient->status);
    }
}