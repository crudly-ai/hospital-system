<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DepartmentControllerTest extends TestCase
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
        Permission::create(['name' => 'view_department']);
        Permission::create(['name' => 'create_department']);
        Permission::create(['name' => 'edit_department']);
        Permission::create(['name' => 'delete_department']);
        Permission::create(['name' => 'manage_all_department']);
        Permission::create(['name' => 'manage_own_department']);
    }

    public function test_index_requires_permission()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/departments');
        
        $response->assertStatus(403);
    }

    public function test_index_returns_departments_for_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_department');
        
        Department::factory()->count(3)->create();
        
        $response = $this->actingAs($user)->get('/departments');
        
        $response->assertStatus(200);
    }

    public function test_store_creates_department_with_valid_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_department');
        
        $data = [
            'name' => 'Test Name',
            'description' => 'Test description',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->post('/departments', $data);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('departments', [
            // Add key assertions here
            'created_by' => $user->id,
        ]);
    }

    public function test_store_requires_permission()
    {
        $user = User::factory()->create();
        
        $data = [
            'name' => 'Test Name',
            'description' => 'Test description',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->post('/departments', $data);
        
        $response->assertStatus(403);
    }

    public function test_store_validates_required_fields()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_department');
        
        $response = $this->actingAs($user)->post('/departments', []);
        
        $response->assertSessionHasErrors(['name', 'status']);
    }

    public function test_show_returns_department_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_department');
        
        $department = Department::factory()->create();
        
        $response = $this->actingAs($user)->get("/departments/{$department->id}");
        
        $response->assertStatus(200);
    }

    public function test_update_modifies_department_with_valid_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_department', 'manage_all_department');
        
        $department = Department::factory()->create();
        
        $data = [
            'name' => 'Test Name',
            'description' => 'Test description',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->put("/departments/{$department->id}", $data);
        
        $response->assertRedirect();
    }

    public function test_update_requires_permission()
    {
        $user = User::factory()->create();
        $department = Department::factory()->create();
        
        $response = $this->actingAs($user)->put("/departments/{$department->id}", []);
        
        $response->assertStatus(403);
    }

    public function test_update_own_department_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['edit_department', 'manage_own_department']);
        
        $department = Department::factory()->create(['created_by' => $user->id]);
        
        $data = [
            'name' => 'Test Name',
            'description' => 'Test description',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->put("/departments/{$department->id}", $data);
        
        $response->assertRedirect();
    }

    public function test_cannot_update_others_department_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->givePermissionTo(['edit_department', 'manage_own_department']);
        
        $department = Department::factory()->create(['created_by' => $otherUser->id]);
        
        $response = $this->actingAs($user)->put("/departments/{$department->id}", []);
        
        $response->assertStatus(403);
    }

    public function test_destroy_deletes_department()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete_department', 'manage_all_department');
        
        $department = Department::factory()->create();
        
        $response = $this->actingAs($user)->delete("/departments/{$department->id}");
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    public function test_destroy_requires_permission()
    {
        $user = User::factory()->create();
        $department = Department::factory()->create();
        
        $response = $this->actingAs($user)->delete("/departments/{$department->id}");
        
        $response->assertStatus(403);
    }

    public function test_destroy_own_department_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['delete_department', 'manage_own_department']);
        
        $department = Department::factory()->create(['created_by' => $user->id]);
        
        $response = $this->actingAs($user)->delete("/departments/{$department->id}");
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    public function test_cannot_delete_others_department_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->givePermissionTo(['delete_department', 'manage_own_department']);
        
        $department = Department::factory()->create(['created_by' => $otherUser->id]);
        
        $response = $this->actingAs($user)->delete("/departments/{$department->id}");
        
        $response->assertStatus(403);
    }
}