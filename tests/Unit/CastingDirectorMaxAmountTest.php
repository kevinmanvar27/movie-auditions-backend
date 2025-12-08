<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use function calculate_casting_director_payment;
use function get_casting_director_max_amount;

class CastingDirectorMaxAmountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed the database with test settings
        DB::table('system_settings')->insertOrIgnore([
            ['key' => 'casting_director_amount', 'value' => '100000'],
            ['key' => 'casting_director_percentage', 'value' => '5'],
            ['key' => 'casting_director_max_amount', 'value' => '5000000'],
        ]);
    }

    /** @test */
    public function it_returns_configured_maximum_amount()
    {
        $maxAmount = get_casting_director_max_amount();
        $this->assertEquals(5000000, $maxAmount);
    }

    /** @test */
    public function it_caps_payment_amount_at_configured_maximum()
    {
        // Test case: Budget of 100 crores = ₹1,000,000,000
        // Fixed amount: ₹100,000
        // Percentage amount: 5% of ₹1,000,000,000 = ₹50,000,000
        // Configured maximum: ₹50,00,000
        // Expected result: ₹50,00,000 (capped amount)
        $amount = calculate_casting_director_payment(100);
        $this->assertEquals(5000000, $amount);
    }

    /** @test */
    public function it_uses_lower_configured_maximum()
    {
        // Update the maximum amount to a lower value
        DB::table('system_settings')->where('key', 'casting_director_max_amount')->update(['value' => '3000000']);
        
        // Test case: Budget of 100 crores = ₹1,000,000,000
        // Fixed amount: ₹100,000
        // Percentage amount: 5% of ₹1,000,000,000 = ₹50,000,000
        // Configured maximum: ₹30,00,000
        // Expected result: ₹30,00,000 (capped amount)
        $amount = calculate_casting_director_payment(100);
        $this->assertEquals(3000000, $amount);
    }

    /** @test */
    public function it_defaults_to_50_lakhs_if_max_amount_not_set()
    {
        // Remove the maximum amount setting
        DB::table('system_settings')->where('key', 'casting_director_max_amount')->delete();
        
        // Should default to ₹50,00,000
        $maxAmount = get_casting_director_max_amount();
        $this->assertEquals(5000000, $maxAmount);
    }
}