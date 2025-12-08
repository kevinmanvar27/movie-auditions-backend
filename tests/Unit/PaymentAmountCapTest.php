<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use function calculate_casting_director_payment;

class PaymentAmountCapTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed the database with test settings
        DB::table('system_settings')->insertOrIgnore([
            ['key' => 'casting_director_amount', 'value' => '100000'],
            ['key' => 'casting_director_percentage', 'value' => '5'],
        ]);
    }

    /** @test */
    public function it_caps_payment_amount_at_50_lakhs()
    {
        // Test case: Budget of 100 crores = ₹1,000,000,000
        // Fixed amount: ₹100,000
        // Percentage amount: 5% of ₹1,000,000,000 = ₹50,000,000
        // Capped amount: ₹50,00,000 (50 lakhs)
        // Expected result: ₹50,00,000 (capped amount)
        $amount = calculate_casting_director_payment(100);
        $this->assertEquals(5000000, $amount);
    }

    /** @test */
    public function it_does_not_cap_when_under_limit()
    {
        // Test case: Budget of 5 crores = ₹50,000,000
        // Fixed amount: ₹100,000
        // Percentage amount: 5% of ₹50,000,000 = ₹2,500,000
        // Expected result: ₹2,500,000 (percentage amount since it's higher than fixed)
        $amount = calculate_casting_director_payment(5);
        $this->assertEquals(2500000, $amount);
    }

    /** @test */
    public function it_uses_fixed_amount_when_higher_than_percentage()
    {
        // Test case: Budget of 0.001 crores = ₹100,000
        // Fixed amount: ₹100,000
        // Percentage amount: 5% of ₹100,000 = ₹5,000
        // Expected result: ₹100,000 (fixed amount since it's higher than percentage)
        $amount = calculate_casting_director_payment(0.001);
        $this->assertEquals(100000, $amount);
    }
}