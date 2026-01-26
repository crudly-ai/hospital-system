<?php

namespace Tests\Unit;

use App\Models\Admission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admission_can_be_created()
    {
        $user = User::factory()->create();
        
        $admission = Admission::create([
            'admitted_on' => '2024-01-01',
            'room_number' => 'Test Room Number',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('admissions', [
            'created_by' => $user->id,
        ]);
    }

    public function test_admission_has_fillable_attributes()
    {
        $admission = new Admission();
        
        $this->assertContains('patient_id', $admission->getFillable());
        $this->assertContains('admitted_on', $admission->getFillable());
        $this->assertContains('discharged_on', $admission->getFillable());
        $this->assertContains('room_number', $admission->getFillable());
        $this->assertContains('status', $admission->getFillable());
        $this->assertContains('created_by', $admission->getFillable());
    }

    public function test_admission_has_proper_casts()
    {
        $admission = new Admission();
        $casts = $admission->getCasts();
        
        $this->assertEquals('date', $casts['admitted_on']);
        $this->assertEquals('date', $casts['discharged_on']);
        $this->assertEquals('string', $casts['status']);
    }

    public function test_admission_belongs_to_patient()
    {
        $patient = Patient::factory()->create();
        $admission = Admission::factory()->create(['patient_id' => $patient->id]);
        
        $this->assertInstanceOf(Patient::class, $admission->patient);
        $this->assertEquals($patient->id, $admission->patient->id);
    }

    public function test_admission_belongs_to_creator()
    {
        $user = User::factory()->create();
        $admission = Admission::factory()->create(['created_by' => $user->id]);
        
        $this->assertInstanceOf(User::class, $admission->creator);
        $this->assertEquals($user->id, $admission->creator->id);
    }

    public function test_admission_factory_creates_valid_admission()
    {
        $admission = Admission::factory()->create();

        $this->assertNotNull($admission->room_number);
        $this->assertNotNull($admission->created_by);
    }

    public function test_admission_factory_active_state()
    {
        $admission = Admission::factory()->active()->create();

        $this->assertEquals('active', $admission->status);
    }

    public function test_admission_factory_inactive_state()
    {
        $admission = Admission::factory()->inactive()->create();

        $this->assertEquals('inactive', $admission->status);
    }
}