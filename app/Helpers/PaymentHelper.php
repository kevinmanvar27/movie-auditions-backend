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