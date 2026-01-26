<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AdmissionControllerTest extends TestCase
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
        Permission::create(['name' => 'view_admission']);
        Permission::create(['name' => 'create_admission']);
        Permission::create(['name' => 'edit_admission']);
        Permission::create(['name' => 'delete_admission']);
        Permission::create(['name' => 'manage_all_admission']);
        Permission::create(['name' => 'manage_own_admission']);
    }

    public function test_index_requires_permission()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/admissions');
        
        $response->assertStatus(403);
    }

    public function test_index_returns_admissions_for_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_admission');
        
        Admission::factory()->count(3)->create();
        
        $response = $this->actingAs($user)->get('/admissions');
        
        $response->assertStatus(200);
    }

    public function test_store_creates_admission_with_valid_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_admission');
        $patient = Patient::factory()->create();
        
        $data = [
            'patient_id' => $patient->id,
            'admitted_on' => '2024-01-01',
            'discharged_on' => '2024-01-01',
            'room_number' => 'Test Room Number',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->post('/admissions', $data);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('admissions', [
            // Add key assertions here
            'created_by' => $user->id,
        ]);
    }

    public function test_store_requires_permission()
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();
        
        $data = [
            'patient_id' => $patient->id,
            'admitted_on' => '2024-01-01',
            'discharged_on' => '2024-01-01',
            'room_number' => 'Test Room Number',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->post('/admissions', $data);
        
        $response->assertStatus(403);
    }

    public function test_store_validates_required_fields()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_admission');
        
        $response = $this->actingAs($user)->post('/admissions', []);
        
        $response->assertSessionHasErrors(['admitted_on', 'room_number', 'status']);
    }

    public function test_show_returns_admission_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_admission');
        
        $admission = Admission::factory()->create();
        
        $response = $this->actingAs($user)->get("/admissions/{$admission->id}");
        
        $response->assertStatus(200);
    }

    public function test_update_modifies_admission_with_valid_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_admission', 'manage_all_admission');
        
        $admission = Admission::factory()->create();
        $patient = Patient::factory()->create();
        
        $data = [
            'patient_id' => $patient->id,
            'admitted_on' => '2024-01-01',
            'discharged_on' => '2024-01-01',
            'room_number' => 'Test Room Number',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->put("/admissions/{$admission->id}", $data);
        
        $response->assertRedirect();
    }

    public function test_update_requires_permission()
    {
        $user = User::factory()->create();
        $admission = Admission::factory()->create();
        
        $response = $this->actingAs($user)->put("/admissions/{$admission->id}", []);
        
        $response->assertStatus(403);
    }

    public function test_update_own_admission_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['edit_admission', 'manage_own_admission']);
        
        $admission = Admission::factory()->create(['created_by' => $user->id]);
        $patient = Patient::factory()->create();
        
        $data = [
            'patient_id' => $patient->id,
            'admitted_on' => '2024-01-01',
            'discharged_on' => '2024-01-01',
            'room_number' => 'Test Room Number',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->put("/admissions/{$admission->id}", $data);
        
        $response->assertRedirect();
    }

    public function test_cannot_update_others_admission_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->givePermissionTo(['edit_admission', 'manage_own_admission']);
        
        $admission = Admission::factory()->create(['created_by' => $otherUser->id]);
        
        $response = $this->actingAs($user)->put("/admissions/{$admission->id}", []);
        
        $response->assertStatus(403);
    }

    public function test_destroy_deletes_admission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete_admission', 'manage_all_admission');
        
        $admission = Admission::factory()->create();
        
        $response = $this->actingAs($user)->delete("/admissions/{$admission->id}");
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('admissions', ['id' => $admission->id]);
    }

    public function test_destroy_requires_permission()
    {
        $user = User::factory()->create();
        $admission = Admission::factory()->create();
        
        $response = $this->actingAs($user)->delete("/admissions/{$admission->id}");
        
        $response->assertStatus(403);
    }

    public function test_destroy_own_admission_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['delete_admission', 'manage_own_admission']);
        
        $admission = Admission::factory()->create(['created_by' => $user->id]);
        
        $response = $this->actingAs($user)->delete("/admissions/{$admission->id}");
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('admissions', ['id' => $admission->id]);
    }

    public function test_cannot_delete_others_admission_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->givePermissionTo(['delete_admission', 'manage_own_admission']);
        
        $admission = Admission::factory()->create(['created_by' => $otherUser->id]);
        
        $response = $this->actingAs($user)->delete("/admissions/{$admission->id}");
        
        $response->assertStatus(403);
    }
}