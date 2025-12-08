<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use function calculate_casting_director_payment;

class BudgetInCroresTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed the database with test settings
        DB::table('system_settings')->insertOrIgnore([
            ['key' => 'casting_director_amount', 'value' => '50000'],
            ['key' => 'casting_director_percentage', 'value' => '5'],
        ]);
    }

    /** @test */
    public function it_calculates_payment_based_on_budget_in_crores()
    {
        // Test case: Budget of 12 crores
        // Fixed amount: ₹50,000
        // Percentage amount: 5% of 12 crores = 5% of ₹120,000,000 = ₹6,000,000
        // Capped amount: ₹50,00,000 (50 lakhs) - Razorpay limit
        // Expected result: ₹50,00,000 (capped amount)
        $amount = calculate_casting_director_payment(12);
        $this->assertEquals(5000000, $amount);
    }

    /** @test */
    public function it_uses_fixed_amount_when_higher_than_percentage()
    {
        // Test case: Budget of 0.001 crores (₹100,000)
        // Fixed amount: ₹50,000
        // Percentage amount: 5% of 0.001 crores = 5% of ₹100,000 = ₹5,000
        // Expected result: ₹50,000 (higher value)
        $amount = calculate_casting_director_payment(0.001);
        $this->assertEquals(50000, $amount);
    }

    /** @test */
    public function it_handles_edge_case_with_zero_budget()
    {
        // Test case: Zero budget
        // Should return fixed amount
        $amount = calculate_casting_director_payment(0);
        $this->assertEquals(50000, $amount);
    }
}