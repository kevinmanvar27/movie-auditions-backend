<?php

use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;

if (!function_exists('get_razorpay_api')) {
    /**
     * Get Razorpay API instance
     *
     * @return \Razorpay\Api\Api|null
     */
    function get_razorpay_api()
    {
        try {
            // Get Razorpay keys from system settings
            $keyId = DB::table('system_settings')->where('key', 'razorpay_key_id')->value('value');
            $keySecret = DB::table('system_settings')->where('key', 'razorpay_key_secret')->value('value');
            
            // Initialize Razorpay API if keys are available
            if ($keyId && $keySecret) {
                return new Api($keyId, $keySecret);
            }
            
            return null;
        } catch (\Exception $e) {
            // Log error and return null if initialization fails
            \Illuminate\Support\Facades\Log::error('Razorpay API initialization failed: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('get_audition_user_amount')) {
    /**
     * Get the audition user payment amount from settings
     *
     * @return float
     */
    function get_audition_user_amount()
    {
        try {
            $amount = DB::table('system_settings')->where('key', 'audition_user_amount')->value('value');
            return $amount ? (float)$amount : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}

if (!function_exists('get_casting_director_amount')) {
    /**
     * Get the casting director payment amount from settings
     *
     * @return float
     */
    function get_casting_director_amount()
    {
        try {
            // First check for fixed amount
            $fixedAmount = DB::table('system_settings')->where('key', 'casting_director_amount')->value('value');
            if ($fixedAmount) {
                return (float)$fixedAmount;
            }
            
            // If no fixed amount, return 0
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}

if (!function_exists('calculate_casting_director_payment')) {
    /**
     * Calculate the casting director payment amount based on the higher value
     * between fixed amount and budget percentage.
     * 
     * The budget is interpreted in crores (e.g., entering "12" means 12 crores = ₹120,000,000).
     * The system compares the fixed amount with the percentage of the total budget and uses whichever is greater.
     * Payment amount is capped at the configured maximum amount to comply with Razorpay limits.
     *
     * @param float|null $budget The movie budget in crores
     * @return float The calculated payment amount in rupees (capped at ₹50,00,000)
     */
    function calculate_casting_director_payment($budget = null)
    {
        try {
            // Get fixed amount from settings
            $fixedAmount = get_casting_director_amount();
            
            // Get percentage from settings
            $percentage = DB::table('system_settings')->where('key', 'casting_director_percentage')->value('value');
            $percentage = $percentage ? (float)$percentage : 0;
            
            // If no budget provided or budget is 0, return fixed amount
            if (!$budget || $budget <= 0) {
                return $fixedAmount;
            }
            
            // If no percentage set, return fixed amount
            if ($percentage <= 0) {
                return $fixedAmount;
            }
            
            // Convert budget from crores to actual amount (1 crore = 10,000,000)
            $budgetInRupees = $budget * 10000000;
            
            // Calculate percentage amount based on the budget in rupees
            $percentageAmount = ($budgetInRupees * $percentage) / 100;
            
            // Return the higher value between fixed amount and percentage amount
            // But if fixed amount is 0, return percentage amount
            if ($fixedAmount <= 0) {
                // Cap the percentage amount at the configured maximum to comply with Razorpay limits
                $maxAmount = get_casting_director_max_amount();
                return min($percentageAmount, $maxAmount);
            }
            
            // Cap the maximum amount at the configured maximum to comply with Razorpay limits
            $maxAmount = get_casting_director_max_amount();
            $finalAmount = max($fixedAmount, $percentageAmount);
            return min($finalAmount, $maxAmount);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error calculating casting director payment: ' . $e->getMessage());
            // Fallback to fixed amount if calculation fails
            $maxAmount = get_casting_director_max_amount();
            return min(get_casting_director_amount(), $maxAmount);
        }
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format currency amount
     *
     * @param float $amount
     * @param string $currency
     * @return string
     */
    function format_currency($amount, $currency = 'INR')
    {
        switch ($currency) {
            case 'INR':
                return '₹' . number_format($amount, 2);
            default:
                return $currency . ' ' . number_format($amount, 2);
        }
    }
}

if (!function_exists('is_casting_director_payment_required')) {
    /**
     * Check if payment is required for casting directors
     *
     * @return bool
     */
    function is_casting_director_payment_required()
    {
        try {
            $isRequired = DB::table('system_settings')->where('key', 'casting_director_payment_required')->value('value');
            return $isRequired == '1';
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('get_casting_director_max_amount')) {
    /**
     * Get the casting director maximum payment amount from settings
     *
     * @return float
     */
    function get_casting_director_max_amount()
    {
        try {
            $maxAmount = DB::table('system_settings')->where('key', 'casting_director_max_amount')->value('value');
            return $maxAmount ? (float)$maxAmount : 5000000; // Default to ₹50,00,000 if not set
        } catch (\Exception $e) {
            return 5000000; // Default to ₹50,00,000 if error occurs
        }
    }
}

if (!function_exists('is_audition_user_payment_required')) {
    /**
     * Check if payment is required for audition users
     *
     * @return bool
     */
    function is_audition_user_payment_required()
    {
        try {
            $isRequired = DB::table('system_settings')->where('key', 'audition_user_payment_required')->value('value');
            return $isRequired == '1';
        } catch (\Exception $e) {
            return false;
        }
    }
}