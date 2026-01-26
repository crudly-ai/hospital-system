<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PatientControllerTest extends TestCase
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
        Permission::create(['name' => 'view_patient']);
        Permission::create(['name' => 'create_patient']);
        Permission::create(['name' => 'edit_patient']);
        Permission::create(['name' => 'delete_patient']);
        Permission::create(['name' => 'manage_all_patient']);
        Permission::create(['name' => 'manage_own_patient']);
    }

    public function test_index_requires_permission()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/patients');
        
        $response->assertStatus(403);
    }

    public function test_index_returns_patients_for_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_patient');
        
        Patient::factory()->count(3)->create();
        
        $response = $this->actingAs($user)->get('/patients');
        
        $response->assertStatus(200);
    }

    public function test_store_creates_patient_with_valid_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_patient');
        
        $data = [
            'name' => 'Test Name',
            'dob' => '2024-01-01',
            'phone' => 'Test Phone',
            'email' => 'test@example.com',
            'address' => 'Test description',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->post('/patients', $data);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('patients', [
            // Add key assertions here
            'created_by' => $user->id,
        ]);
    }

    public function test_store_requires_permission()
    {
        $user = User::factory()->create();
        
        $data = [
            'name' => 'Test Name',
            'dob' => '2024-01-01',
            'phone' => 'Test Phone',
            'email' => 'test@example.com',
            'address' => 'Test description',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->post('/patients', $data);
        
        $response->assertStatus(403);
    }

    public function test_store_validates_required_fields()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_patient');
        
        $response = $this->actingAs($user)->post('/patients', []);
        
        $response->assertSessionHasErrors(['name', 'status']);
    }

    public function test_show_returns_patient_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_patient');
        
        $patient = Patient::factory()->create();
        
        $response = $this->actingAs($user)->get("/patients/{$patient->id}");
        
        $response->assertStatus(200);
    }

    public function test_update_modifies_patient_with_valid_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_patient', 'manage_all_patient');
        
        $patient = Patient::factory()->create();
        
        $data = [
            'name' => 'Test Name',
            'dob' => '2024-01-01',
            'phone' => 'Test Phone',
            'email' => 'test@example.com',
            'address' => 'Test description',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->put("/patients/{$patient->id}", $data);
        
        $response->assertRedirect();
    }

    public function test_update_requires_permission()
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();
        
        $response = $this->actingAs($user)->put("/patients/{$patient->id}", []);
        
        $response->assertStatus(403);
    }

    public function test_update_own_patient_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['edit_patient', 'manage_own_patient']);
        
        $patient = Patient::factory()->create(['created_by' => $user->id]);
        
        $data = [
            'name' => 'Test Name',
            'dob' => '2024-01-01',
            'phone' => 'Test Phone',
            'email' => 'test@example.com',
            'address' => 'Test description',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->put("/patients/{$patient->id}", $data);
        
        $response->assertRedirect();
    }

    public function test_cannot_update_others_patient_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->givePermissionTo(['edit_patient', 'manage_own_patient']);
        
        $patient = Patient::factory()->create(['created_by' => $otherUser->id]);
        
        $response = $this->actingAs($user)->put("/patients/{$patient->id}", []);
        
        $response->assertStatus(403);
    }

    public function test_destroy_deletes_patient()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete_patient', 'manage_all_patient');
        
        $patient = Patient::factory()->create();
        
        $response = $this->actingAs($user)->delete("/patients/{$patient->id}");
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('patients', ['id' => $patient->id]);
    }

    public function test_destroy_requires_permission()
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();
        
        $response = $this->actingAs($user)->delete("/patients/{$patient->id}");
        
        $response->assertStatus(403);
    }

    public function test_destroy_own_patient_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['delete_patient', 'manage_own_patient']);
        
        $patient = Patient::factory()->create(['created_by' => $user->id]);
        
        $response = $this->actingAs($user)->delete("/patients/{$patient->id}");
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('patients', ['id' => $patient->id]);
    }

    public function test_cannot_delete_others_patient_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->givePermissionTo(['delete_patient', 'manage_own_patient']);
        
        $patient = Patient::factory()->create(['created_by' => $otherUser->id]);
        
        $response = $this->actingAs($user)->delete("/patients/{$patient->id}");
        
        $response->assertStatus(403);
    }
}