<?php

namespace Tests\Unit;

use App\Models\Billing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    public function test_billing_can_be_created()
    {
        $user = User::factory()->create();
        
        $billing = Billing::create([
            'amount' => 100.00,
            'payment_date' => '2024-01-01',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('billings', [
            'created_by' => $user->id,
        ]);
    }

    public function test_billing_has_fillable_attributes()
    {
        $billing = new Billing();
        
        $this->assertContains('patient_id', $billing->getFillable());
        $this->assertContains('amount', $billing->getFillable());
        $this->assertContains('payment_date', $billing->getFillable());
        $this->assertContains('status', $billing->getFillable());
        $this->assertContains('created_by', $billing->getFillable());
    }

    public function test_billing_has_proper_casts()
    {
        $billing = new Billing();
        $casts = $billing->getCasts();
        
        $this->assertEquals('decimal:2', $casts['amount']);
        $this->assertEquals('date', $casts['payment_date']);
        $this->assertEquals('string', $casts['status']);
    }

    public function test_billing_belongs_to_patient()
    {
        $patient = Patient::factory()->create();
        $billing = Billing::factory()->create(['patient_id' => $patient->id]);
        
        $this->assertInstanceOf(Patient::class, $billing->patient);
        $this->assertEquals($patient->id, $billing->patient->id);
    }

    public function test_billing_belongs_to_creator()
    {
        $user = User::factory()->create();
        $billing = Billing::factory()->create(['created_by' => $user->id]);
        
        $this->assertInstanceOf(User::class, $billing->creator);
        $this->assertEquals($user->id, $billing->creator->id);
    }

    public function test_billing_factory_creates_valid_billing()
    {
        $billing = Billing::factory()->create();

        $this->assertNotNull($billing->created_by);
    }

    public function test_billing_factory_active_state()
    {
        $billing = Billing::factory()->active()->create();

        $this->assertEquals('active', $billing->status);
    }

    public function test_billing_factory_inactive_state()
    {
        $billing = Billing::factory()->inactive()->create();

        $this->assertEquals('inactive', $billing->status);
    }
}