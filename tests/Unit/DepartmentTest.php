<?php

namespace Tests\Unit;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_department_can_be_created()
    {
        $user = User::factory()->create();
        
        $department = Department::create([
            'name' => 'Test Name',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('departments', [
            'name' => 'Test Name',
            'created_by' => $user->id,
        ]);
    }

    public function test_department_has_fillable_attributes()
    {
        $department = new Department();
        
        $this->assertContains('name', $department->getFillable());
        $this->assertContains('description', $department->getFillable());
        $this->assertContains('status', $department->getFillable());
        $this->assertContains('created_by', $department->getFillable());
    }

    public function test_department_has_proper_casts()
    {
        $department = new Department();
        $casts = $department->getCasts();
        
        $this->assertEquals('string', $casts['status']);
    }

    public function test_department_belongs_to_creator()
    {
        $user = User::factory()->create();
        $department = Department::factory()->create(['created_by' => $user->id]);
        
        $this->assertInstanceOf(User::class, $department->creator);
        $this->assertEquals($user->id, $department->creator->id);
    }

    public function test_department_factory_creates_valid_department()
    {
        $department = Department::factory()->create();

        $this->assertNotNull($department->name);
        $this->assertNotNull($department->created_by);
    }

    public function test_department_factory_active_state()
    {
        $department = Department::factory()->active()->create();

        $this->assertEquals('active', $department->status);
    }

    public function test_department_factory_inactive_state()
    {
        $department = Department::factory()->inactive()->create();

        $this->assertEquals('inactive', $department->status);
    }
}