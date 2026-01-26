<?php

namespace Tests\Unit;

use App\Models\Treatment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TreatmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_treatment_can_be_created()
    {
        $user = User::factory()->create();
        
        $treatment = Treatment::create([
            'name' => 'Test Name',
            'cost' => 100.00,
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('treatments', [
            'name' => 'Test Name',
            'created_by' => $user->id,
        ]);
    }

    public function test_treatment_has_fillable_attributes()
    {
        $treatment = new Treatment();
        
        $this->assertContains('name', $treatment->getFillable());
        $this->assertContains('description', $treatment->getFillable());
        $this->assertContains('cost', $treatment->getFillable());
        $this->assertContains('status', $treatment->getFillable());
        $this->assertContains('created_by', $treatment->getFillable());
    }

    public function test_treatment_has_proper_casts()
    {
        $treatment = new Treatment();
        $casts = $treatment->getCasts();
        
        $this->assertEquals('decimal:2', $casts['cost']);
        $this->assertEquals('string', $casts['status']);
    }

    public function test_treatment_belongs_to_creator()
    {
        $user = User::factory()->create();
        $treatment = Treatment::factory()->create(['created_by' => $user->id]);
        
        $this->assertInstanceOf(User::class, $treatment->creator);
        $this->assertEquals($user->id, $treatment->creator->id);
    }

    public function test_treatment_factory_creates_valid_treatment()
    {
        $treatment = Treatment::factory()->create();

        $this->assertNotNull($treatment->name);
        $this->assertNotNull($treatment->created_by);
    }

    public function test_treatment_factory_active_state()
    {
        $treatment = Treatment::factory()->active()->create();

        $this->assertEquals('active', $treatment->status);
    }

    public function test_treatment_factory_inactive_state()
    {
        $treatment = Treatment::factory()->inactive()->create();

        $this->assertEquals('inactive', $treatment->status);
    }
}