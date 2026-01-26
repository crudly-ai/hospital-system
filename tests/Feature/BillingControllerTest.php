<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BillingControllerTest extends TestCase
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
        Permission::create(['name' => 'view_billing']);
        Permission::create(['name' => 'create_billing']);
        Permission::create(['name' => 'edit_billing']);
        Permission::create(['name' => 'delete_billing']);
        Permission::create(['name' => 'manage_all_billing']);
        Permission::create(['name' => 'manage_own_billing']);
    }

    public function test_index_requires_permission()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/billings');
        
        $response->assertStatus(403);
    }

    public function test_index_returns_billings_for_authorized_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_billing');
        
        Billing::factory()->count(3)->create();
        
        $response = $this->actingAs($user)->get('/billings');
        
        $response->assertStatus(200);
    }

    public function test_store_creates_billing_with_valid_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_billing');
        $patient = Patient::factory()->create();
        
        $data = [
            'patient_id' => $patient->id,
            'amount' => 100.00,
            'payment_date' => '2024-01-01',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->post('/billings', $data);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('billings', [
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
            'amount' => 100.00,
            'payment_date' => '2024-01-01',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->post('/billings', $data);
        
        $response->assertStatus(403);
    }

    public function test_store_validates_required_fields()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create_billing');
        
        $response = $this->actingAs($user)->post('/billings', []);
        
        $response->assertSessionHasErrors(['amount', 'payment_date', 'status']);
    }

    public function test_show_returns_billing_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view_billing');
        
        $billing = Billing::factory()->create();
        
        $response = $this->actingAs($user)->get("/billings/{$billing->id}");
        
        $response->assertStatus(200);
    }

    public function test_update_modifies_billing_with_valid_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit_billing', 'manage_all_billing');
        
        $billing = Billing::factory()->create();
        $patient = Patient::factory()->create();
        
        $data = [
            'patient_id' => $patient->id,
            'amount' => 100.00,
            'payment_date' => '2024-01-01',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->put("/billings/{$billing->id}", $data);
        
        $response->assertRedirect();
    }

    public function test_update_requires_permission()
    {
        $user = User::factory()->create();
        $billing = Billing::factory()->create();
        
        $response = $this->actingAs($user)->put("/billings/{$billing->id}", []);
        
        $response->assertStatus(403);
    }

    public function test_update_own_billing_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['edit_billing', 'manage_own_billing']);
        
        $billing = Billing::factory()->create(['created_by' => $user->id]);
        $patient = Patient::factory()->create();
        
        $data = [
            'patient_id' => $patient->id,
            'amount' => 100.00,
            'payment_date' => '2024-01-01',
            'status' => 'active',
        ];
        
        $response = $this->actingAs($user)->put("/billings/{$billing->id}", $data);
        
        $response->assertRedirect();
    }

    public function test_cannot_update_others_billing_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->givePermissionTo(['edit_billing', 'manage_own_billing']);
        
        $billing = Billing::factory()->create(['created_by' => $otherUser->id]);
        
        $response = $this->actingAs($user)->put("/billings/{$billing->id}", []);
        
        $response->assertStatus(403);
    }

    public function test_destroy_deletes_billing()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete_billing', 'manage_all_billing');
        
        $billing = Billing::factory()->create();
        
        $response = $this->actingAs($user)->delete("/billings/{$billing->id}");
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('billings', ['id' => $billing->id]);
    }

    public function test_destroy_requires_permission()
    {
        $user = User::factory()->create();
        $billing = Billing::factory()->create();
        
        $response = $this->actingAs($user)->delete("/billings/{$billing->id}");
        
        $response->assertStatus(403);
    }

    public function test_destroy_own_billing_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['delete_billing', 'manage_own_billing']);
        
        $billing = Billing::factory()->create(['created_by' => $user->id]);
        
        $response = $this->actingAs($user)->delete("/billings/{$billing->id}");
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('billings', ['id' => $billing->id]);
    }

    public function test_cannot_delete_others_billing_with_manage_own_permission()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->givePermissionTo(['delete_billing', 'manage_own_billing']);
        
        $billing = Billing::factory()->create(['created_by' => $otherUser->id]);
        
        $response = $this->actingAs($user)->delete("/billings/{$billing->id}");
        
        $response->assertStatus(403);
    }
}