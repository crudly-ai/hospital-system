<?php

namespace Tests\Unit;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_be_created()
    {
        $user = User::factory()->create();
        
        $doctor = Doctor::create([
            'name' => 'Test Name',
            'specialty' => 'Test Specialty',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('doctors', [
            'name' => 'Test Name',
            'created_by' => $user->id,
        ]);
    }

    public function test_doctor_has_fillable_attributes()
    {
        $doctor = new Doctor();
        
        $this->assertContains('name', $doctor->getFillable());
        $this->assertContains('specialty', $doctor->getFillable());
        $this->assertContains('department_id', $doctor->getFillable());
        $this->assertContains('phone', $doctor->getFillable());
        $this->assertContains('email', $doctor->getFillable());
        $this->assertContains('status', $doctor->getFillable());
        $this->assertContains('created_by', $doctor->getFillable());
    }

    public function test_doctor_has_proper_casts()
    {
        $doctor = new Doctor();
        $casts = $doctor->getCasts();
        
        $this->assertEquals('string', $casts['status']);
    }

    public function test_doctor_belongs_to_department()
    {
        $department = Department::factory()->create();
        $doctor = Doctor::factory()->create(['department_id' => $department->id]);
        
        $this->assertInstanceOf(Department::class, $doctor->department);
        $this->assertEquals($department->id, $doctor->department->id);
    }

    public function test_doctor_belongs_to_creator()
    {
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create(['created_by' => $user->id]);
        
        $this->assertInstanceOf(User::class, $doctor->creator);
        $this->assertEquals($user->id, $doctor->creator->id);
    }

    public function test_doctor_factory_creates_valid_doctor()
    {
        $doctor = Doctor::factory()->create();

        $this->assertNotNull($doctor->name);
        $this->assertNotNull($doctor->specialty);
        $this->assertNotNull($doctor->created_by);
    }

    public function test_doctor_factory_active_state()
    {
        $doctor = Doctor::factory()->active()->create();

        $this->assertEquals('active', $doctor->status);
    }

    public function test_doctor_factory_inactive_state()
    {
        $doctor = Doctor::factory()->inactive()->create();

        $this->assertEquals('inactive', $doctor->status);
    }
}