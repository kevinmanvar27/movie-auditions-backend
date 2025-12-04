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
     * between fixed amount and budget percentage
     *
     * @param float|null $budget The movie budget
     * @return float
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
            
            // Calculate percentage amount
            $percentageAmount = ($budget * $percentage) / 100;
            
            // Return the higher value between fixed amount and percentage amount
            // But if fixed amount is 0, return percentage amount
            if ($fixedAmount <= 0) {
                return $percentageAmount;
            }
            
            return max($fixedAmount, $percentageAmount);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error calculating casting director payment: ' . $e->getMessage());
            // Fallback to fixed amount if calculation fails
            return get_casting_director_amount();
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