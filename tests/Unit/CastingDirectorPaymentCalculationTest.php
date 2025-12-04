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
    public function it_returns_percentage_amount_when_it_is_higher_than_fixed_amount()
    {
        // Budget: 2000, Percentage: 10% = 200, Fixed: 100
        // Should return 200 (higher value)
        $amount = calculate_casting_director_payment(2000);
        $this->assertEquals(200, $amount);
    }

    /** @test */
    public function it_returns_fixed_amount_when_it_is_higher_than_percentage_amount()
    {
        // Budget: 500, Percentage: 10% = 50, Fixed: 100
        // Should return 100 (higher value)
        $amount = calculate_casting_director_payment(500);
        $this->assertEquals(100, $amount);
    }

    /** @test */
    public function it_returns_percentage_amount_when_fixed_amount_is_zero()
    {
        // Update fixed amount to 0
        DB::table('system_settings')->where('key', 'casting_director_amount')->update(['value' => '0']);
        
        // Budget: 500, Percentage: 10% = 50, Fixed: 0
        // Should return 50 (percentage amount since fixed is 0)
        $amount = calculate_casting_director_payment(500);
        $this->assertEquals(50, $amount);
    }

    /** @test */
    public function it_returns_zero_when_both_fixed_and_percentage_are_zero()
    {
        // Update settings to zero
        DB::table('system_settings')->where('key', 'casting_director_amount')->update(['value' => '0']);
        DB::table('system_settings')->where('key', 'casting_director_percentage')->update(['value' => '0']);
        
        $amount = calculate_casting_director_payment(500);
        $this->assertEquals(0, $amount);
    }
}