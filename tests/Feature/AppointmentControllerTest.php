<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AppointmentControllerTest extends TestCase
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
        Permission::create(['name' => 'view_appointment']);
        Permission::create(['name' => 'create_appointment']);
        Permission::create(['name' => 'edit_appointment']);
        Permission::create(['name' => 'delete_appointment']);
        Permission::create(['name' => 'manage_all_appointment']);
        Permission::create(['name' => 'manage_own_appointment']);
    }

    public function test_index_requires_permission()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/appointments');
        
        $response->assertStatus(403);
    }

    public function test_index_returns_appointments_for_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_appointment');
        
        Appointment::factory()->count(3)->create();
        
        $response = $this->actingAs($user)->get('/appointments');
        
        $response->assertStatus(200);
    }

    public function test_store_creates_appointment_with_valid_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_appointment');
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();
        
        $data = [
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'date' => '2024-01-01',
            'time' => '09:00',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->post('/appointments', $data);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            // Add key assertions here
            'created_by' => $user->id,
        ]);
    }

    public function test_store_requires_permission()
    {
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();
        
        $data = [
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'date' => '2024-01-01',
            'time' => '09:00',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->post('/appointments', $data);
        
        $response->assertStatus(403);
    }

    public function test_store_validates_required_fields()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_appointment');
        
        $response = $this->actingAs($user)->post('/appointments', []);
        
        $response->assertSessionHasErrors(['date', 'time', 'status']);
    }

    public function test_show_returns_appointment_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_appointment');
        
        $appointment = Appointment::factory()->create();
        
        $response = $this->actingAs($user)->get("/appointments/{$appointment->id}");
        
        $response->assertStatus(200);
    }

    public function test_update_modifies_appointment_with_valid_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_appointment', 'manage_all_appointment');
        
        $appointment = Appointment::factory()->create();
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();
        
        $data = [
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'date' => '2024-01-01',
            'time' => '09:00',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->put("/appointments/{$appointment->id}", $data);
        
        $response->assertRedirect();
    }

    public function test_update_requires_permission()
    {
        $user = User::factory()->create();
        $appointment = Appointment::factory()->create();
        
        $response = $this->actingAs($user)->put("/appointments/{$appointment->id}", []);
        
        $response->assertStatus(403);
    }

    public function test_update_own_appointment_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['edit_appointment', 'manage_own_appointment']);
        
        $appointment = Appointment::factory()->create(['created_by' => $user->id]);
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();
        
        $data = [
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'date' => '2024-01-01',
            'time' => '09:00',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->put("/appointments/{$appointment->id}", $data);
        
        $response->assertRedirect();
    }

    public function test_cannot_update_others_appointment_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->givePermissionTo(['edit_appointment', 'manage_own_appointment']);
        
        $appointment = Appointment::factory()->create(['created_by' => $otherUser->id]);
        
        $response = $this->actingAs($user)->put("/appointments/{$appointment->id}", []);
        
        $response->assertStatus(403);
    }

    public function test_destroy_deletes_appointment()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete_appointment', 'manage_all_appointment');
        
        $appointment = Appointment::factory()->create();
        
        $response = $this->actingAs($user)->delete("/appointments/{$appointment->id}");
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('appointments', ['id' => $appointment->id]);
    }

    public function test_destroy_requires_permission()
    {
        $user = User::factory()->create();
        $appointment = Appointment::factory()->create();
        
        $response = $this->actingAs($user)->delete("/appointments/{$appointment->id}");
        
        $response->assertStatus(403);
    }

    public function test_destroy_own_appointment_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['delete_appointment', 'manage_own_appointment']);
        
        $appointment = Appointment::factory()->create(['created_by' => $user->id]);
        
        $response = $this->actingAs($user)->delete("/appointments/{$appointment->id}");
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('appointments', ['id' => $appointment->id]);
    }

    public function test_cannot_delete_others_appointment_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->givePermissionTo(['delete_appointment', 'manage_own_appointment']);
        
        $appointment = Appointment::factory()->create(['created_by' => $otherUser->id]);
        
        $response = $this->actingAs($user)->delete("/appointments/{$appointment->id}");
        
        $response->assertStatus(403);
    }
}