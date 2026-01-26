<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\User;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DoctorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // CRITICAL: DO NOT CHANGE THIS LINE - FIXES CSRF 419 ERRORS
        // This disables CSRF middleware while preserving auth and permission middleware
        // DO NOT MODIFY: This has been tested and works correctly
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        
        // Create permissions
        Permission::create(['name' => 'view_doctor']);
        Permission::create(['name' => 'create_doctor']);
        Permission::create(['name' => 'edit_doctor']);
        Permission::create(['name' => 'delete_doctor']);
        Permission::create(['name' => 'manage_all_doctor']);
        Permission::create(['name' => 'manage_own_doctor']);
    }

    public function test_index_requires_permission()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/doctors');
        
        $response->assertStatus(403);
    }

    public function test_index_returns_doctors_for_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_doctor');
        
        Doctor::factory()->count(3)->create();
        
        $response = $this->actingAs($user)->get('/doctors');
        
        $response->assertStatus(200);
    }

    public function test_store_creates_doctor_with_valid_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_doctor');
        $department = Department::factory()->create();
        
        $data = [
            'name' => 'Test Name',
            'specialty' => 'Test Specialty',
            'department_id' => $department->id,
            'phone' => 'Test Phone',
            'email' => 'test@example.com',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->post('/doctors', $data);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('doctors', [
            // Add key assertions here
            'created_by' => $user->id,
        ]);
    }

    public function test_store_requires_permission()
    {
        $user = User::factory()->create();
        $department = Department::factory()->create();
        
        $data = [
            'name' => 'Test Name',
            'specialty' => 'Test Specialty',
            'department_id' => $department->id,
            'phone' => 'Test Phone',
            'email' => 'test@example.com',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->post('/doctors', $data);
        
        $response->assertStatus(403);
    }

    public function test_store_validates_required_fields()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_doctor');
        
        $response = $this->actingAs($user)->post('/doctors', []);
        
        $response->assertSessionHasErrors(['name', 'specialty', 'status']);
    }

    public function test_show_returns_doctor_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_doctor');
        
        $doctor = Doctor::factory()->create();
        
        $response = $this->actingAs($user)->get("/doctors/{$doctor->id}");
        
        $response->assertStatus(200);
    }

    public function test_update_modifies_doctor_with_valid_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_doctor', 'manage_all_doctor');
        
        $doctor = Doctor::factory()->create();
        $department = Department::factory()->create();
        
        $data = [
            'name' => 'Test Name',
            'specialty' => 'Test Specialty',
            'department_id' => $department->id,
            'phone' => 'Test Phone',
            'email' => 'test@example.com',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->put("/doctors/{$doctor->id}", $data);
        
        $response->assertRedirect();
    }

    public function test_update_requires_permission()
    {
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create();
        
        $response = $this->actingAs($user)->put("/doctors/{$doctor->id}", []);
        
        $response->assertStatus(403);
    }

    public function test_update_own_doctor_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['edit_doctor', 'manage_own_doctor']);
        
        $doctor = Doctor::factory()->create(['created_by' => $user->id]);
        $department = Department::factory()->create();
        
        $data = [
            'name' => 'Test Name',
            'specialty' => 'Test Specialty',
            'department_id' => $department->id,
            'phone' => 'Test Phone',
            'email' => 'test@example.com',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->put("/doctors/{$doctor->id}", $data);
        
        $response->assertRedirect();
    }

    public function test_cannot_update_others_doctor_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->givePermissionTo(['edit_doctor', 'manage_own_doctor']);
        
        $doctor = Doctor::factory()->create(['created_by' => $otherUser->id]);
        
        $response = $this->actingAs($user)->put("/doctors/{$doctor->id}", []);
        
        $response->assertStatus(403);
    }

    public function test_destroy_deletes_doctor()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete_doctor', 'manage_all_doctor');
        
        $doctor = Doctor::factory()->create();
        
        $response = $this->actingAs($user)->delete("/doctors/{$doctor->id}");
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('doctors', ['id' => $doctor->id]);
    }

    public function test_destroy_requires_permission()
    {
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create();
        
        $response = $this->actingAs($user)->delete("/doctors/{$doctor->id}");
        
        $response->assertStatus(403);
    }

    public function test_destroy_own_doctor_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['delete_doctor', 'manage_own_doctor']);
        
        $doctor = Doctor::factory()->create(['created_by' => $user->id]);
        
        $response = $this->actingAs($user)->delete("/doctors/{$doctor->id}");
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('doctors', ['id' => $doctor->id]);
    }

    public function test_cannot_delete_others_doctor_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->givePermissionTo(['delete_doctor', 'manage_own_doctor']);
        
        $doctor = Doctor::factory()->create(['created_by' => $otherUser->id]);
        
        $response = $this->actingAs($user)->delete("/doctors/{$doctor->id}");
        
        $response->assertStatus(403);
    }
}