<?php

namespace Tests\Feature;

use App\Models\Treatment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class TreatmentControllerTest extends TestCase
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
        Permission::create(['name' => 'view_treatment']);
        Permission::create(['name' => 'create_treatment']);
        Permission::create(['name' => 'edit_treatment']);
        Permission::create(['name' => 'delete_treatment']);
        Permission::create(['name' => 'manage_all_treatment']);
        Permission::create(['name' => 'manage_own_treatment']);
    }

    public function test_index_requires_permission()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/treatments');
        
        $response->assertStatus(403);
    }

    public function test_index_returns_treatments_for_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_treatment');
        
        Treatment::factory()->count(3)->create();
        
        $response = $this->actingAs($user)->get('/treatments');
        
        $response->assertStatus(200);
    }

    public function test_store_creates_treatment_with_valid_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_treatment');
        
        $data = [
            'name' => 'Test Name',
            'description' => 'Test description',
            'cost' => 100.00,
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->post('/treatments', $data);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('treatments', [
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
            'cost' => 100.00,
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->post('/treatments', $data);
        
        $response->assertStatus(403);
    }

    public function test_store_validates_required_fields()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_treatment');
        
        $response = $this->actingAs($user)->post('/treatments', []);
        
        $response->assertSessionHasErrors(['name', 'cost', 'status']);
    }

    public function test_show_returns_treatment_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_treatment');
        
        $treatment = Treatment::factory()->create();
        
        $response = $this->actingAs($user)->get("/treatments/{$treatment->id}");
        
        $response->assertStatus(200);
    }

    public function test_update_modifies_treatment_with_valid_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_treatment', 'manage_all_treatment');
        
        $treatment = Treatment::factory()->create();
        
        $data = [
            'name' => 'Test Name',
            'description' => 'Test description',
            'cost' => 100.00,
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->put("/treatments/{$treatment->id}", $data);
        
        $response->assertRedirect();
    }

    public function test_update_requires_permission()
    {
        $user = User::factory()->create();
        $treatment = Treatment::factory()->create();
        
        $response = $this->actingAs($user)->put("/treatments/{$treatment->id}", []);
        
        $response->assertStatus(403);
    }

    public function test_update_own_treatment_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['edit_treatment', 'manage_own_treatment']);
        
        $treatment = Treatment::factory()->create(['created_by' => $user->id]);
        
        $data = [
            'name' => 'Test Name',
            'description' => 'Test description',
            'cost' => 100.00,
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->put("/treatments/{$treatment->id}", $data);
        
        $response->assertRedirect();
    }

    public function test_cannot_update_others_treatment_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->givePermissionTo(['edit_treatment', 'manage_own_treatment']);
        
        $treatment = Treatment::factory()->create(['created_by' => $otherUser->id]);
        
        $response = $this->actingAs($user)->put("/treatments/{$treatment->id}", []);
        
        $response->assertStatus(403);
    }

    public function test_destroy_deletes_treatment()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete_treatment', 'manage_all_treatment');
        
        $treatment = Treatment::factory()->create();
        
        $response = $this->actingAs($user)->delete("/treatments/{$treatment->id}");
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('treatments', ['id' => $treatment->id]);
    }

    public function test_destroy_requires_permission()
    {
        $user = User::factory()->create();
        $treatment = Treatment::factory()->create();
        
        $response = $this->actingAs($user)->delete("/treatments/{$treatment->id}");
        
        $response->assertStatus(403);
    }

    public function test_destroy_own_treatment_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['delete_treatment', 'manage_own_treatment']);
        
        $treatment = Treatment::factory()->create(['created_by' => $user->id]);
        
        $response = $this->actingAs($user)->delete("/treatments/{$treatment->id}");
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('treatments', ['id' => $treatment->id]);
    }

    public function test_cannot_delete_others_treatment_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->givePermissionTo(['delete_treatment', 'manage_own_treatment']);
        
        $treatment = Treatment::factory()->create(['created_by' => $otherUser->id]);
        
        $response = $this->actingAs($user)->delete("/treatments/{$treatment->id}");
        
        $response->assertStatus(403);
    }
}