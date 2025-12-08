<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use function calculate_casting_director_payment;

class CastingDirectorPaymentCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed the database with test settings
        DB::table('system_settings')->insertOrIgnore([
            ['key' => 'casting_director_amount', 'value' => '100'],
            ['key' => 'casting_director_percentage', 'value' => '10'],
        ]);
    }

    /** @test */
    public function it_returns_fixed_amount_when_no_budget_is_provided()
    {
        $amount = calculate_casting_director_payment(null);
        $this->assertEquals(100, $amount);
    }

    /** @test */
    public function it_returns_fixed_amount_when_budget_is_zero()
    {
        $amount = calculate_casting_director_payment(0);
        $this->assertEquals(100, $amount);
    }

    /** @test */
    public function it_returns_capped_percentage_amount_when_it_is_higher_than_fixed_amount()
    {
        // Budget: 20 crores = 200000000, Percentage: 10% = 20000000, Fixed: 100
        // Capped at ₹50,00,000 due to Razorpay limits
        // Should return 5000000 (capped amount)
        $amount = calculate_casting_director_payment(20);
        $this->assertEquals(5000000, $amount);
    }

    /** @test */
    public function it_returns_fixed_amount_when_it_is_higher_than_percentage_amount()
    {
        // Budget: 0.00001 crores = 100, Percentage: 10% = 10, Fixed: 100
        // Should return 100 (higher value)
        $amount = calculate_casting_director_payment(0.00001);
        $this->assertEquals(100, $amount);
    }

    /** @test */
    public function it_returns_percentage_amount_when_fixed_amount_is_zero()
    {
        // Update fixed amount to 0
        DB::table('system_settings')->where('key', 'casting_director_amount')->update(['value' => '0']);
        
        // Budget: 5 crores = 50000000, Percentage: 10% = 5000000, Fixed: 0
        // Should return 5000000 (percentage amount since fixed is 0)
        $amount = calculate_casting_director_payment(5);
        $this->assertEquals(5000000, $amount);
    }

    /** @test */
    public function it_returns_zero_when_both_fixed_and_percentage_are_zero()
    {
        // Update settings to zero
        DB::table('system_settings')->where('key', 'casting_director_amount')->update(['value' => '0']);
        DB::table('system_settings')->where('key', 'casting_director_percentage')->update(['value' => '0']);
        
        $amount = calculate_casting_director_payment(5);
        $this->assertEquals(0, $amount);
    }
}