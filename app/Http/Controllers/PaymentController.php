<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SystemSetting;

class PaymentController extends Controller
{
    protected $api;

    /**
     * Get Razorpay API instance
     */
    private function getRazorpayApi()
    {
        try {
            // Initialize Razorpay API with key and secret from database settings
            $keyRecord = DB::table('system_settings')->where('key', 'razorpay_key_id')->first();
            $secretRecord = DB::table('system_settings')->where('key', 'razorpay_key_secret')->first();
            
            // Extract values safely
            $key = $keyRecord ? $keyRecord->value : null;
            $secret = $secretRecord ? $secretRecord->value : null;
            
            // Trim whitespace and check if keys are valid
            $key = trim($key ?? '');
            $secret = trim($secret ?? '');
            
            Log::info('Retrieved Razorpay keys', [
                'key_id_present' => !empty($key),
                'key_secret_present' => !empty($secret),
                'key_id_length' => strlen($key),
                'key_secret_length' => strlen($secret)
            ]);
            
            if ($key && $secret && strlen($key) > 5 && strlen($secret) > 5) {
                Log::info('Initializing Razorpay API with key: ' . substr($key, 0, 10) . '...');
                $api = new Api($key, $secret);
                
                // Set custom options for better reliability
                // This can help with timeout and network issues
                return $api;
            } else {
                Log::warning('Razorpay keys not properly configured', [
                    'key_id_set' => !empty($key),
                    'key_secret_set' => !empty($secret),
                    'key_id_length' => strlen($key ?? ''),
                    'key_secret_length' => strlen($secret ?? ''),
                    'key_id_value' => $key ?? 'NULL',
                    'key_secret_value' => $secret ? 'SET ('.strlen($secret).' chars)' : 'NOT SET'
                ]);
                
                // Provide more specific error messages
                if (empty($key) || strlen($key) <= 5) {
                    Log::warning('Razorpay Key ID is missing or invalid');
                }
                if (empty($secret) || strlen($secret) <= 5) {
                    Log::warning('Razorpay Key Secret is missing or invalid');
                }
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving Razorpay keys: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        return null;
    }

    /**
     * Create a payment order for audition submission
     */
    public function createAuditionPaymentOrder(Request $request)
    {
        try {
            Log::info('Creating audition payment order');
            
            // Get Razorpay API instance
            $api = $this->getRazorpayApi();
            
            // Check if Razorpay API is initialized
            if (!$api) {
                Log::error('Razorpay API not initialized for audition payment');
                return response()->json([
                    'success' => false,
                    'message' => 'Payment gateway not properly configured. Please ensure Razorpay API keys are set in the admin settings.'
                ], 500);
            }
            
            // Get audition payment amount from settings
            $amount = $this->getAuditionUserAmount();
            Log::info('Audition payment amount: ' . $amount);
            
            if ($amount <= 0) {
                Log::warning('Invalid audition payment amount: ' . $amount);
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount not configured properly.'
                ], 400);
            }
            
            // Convert amount to paise (smallest currency unit)
            $amountInPaise = $amount * 100;
            Log::info('Amount in paise: ' . $amountInPaise);
            
            // Create Razorpay order
            $orderData = [
                'receipt'         => 'audition_' . time(),
                'amount'          => $amountInPaise, // Amount in paise
                'currency'        => 'INR',
                'payment_capture' => 1 // Auto-capture payment
            ];
            
            Log::info('Creating Razorpay order with data: ', $orderData);

            // Add timeout and error handling
            $razorpayOrder = null;
            try {
                $razorpayOrder = $api->order->create($orderData);
                Log::info('Raw Razorpay order response: ', ['response' => $razorpayOrder]);
            } catch (\Exception $e) {
                Log::error('Exception during Razorpay order creation: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e; // Re-throw to be caught by outer catch block
            }
            
            // Check if order was created successfully
            if (!$razorpayOrder) {
                Log::error('Razorpay order creation returned null');
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment order. Service unavailable.'
                ], 500);
            }
            
            // Ensure we have the expected structure
            if (!is_array($razorpayOrder) && !($razorpayOrder instanceof \ArrayAccess)) {
                Log::error('Razorpay order response is not array-like', ['type' => gettype($razorpayOrder)]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment order. Invalid response format.'
                ], 500);
            }
            
            if (!isset($razorpayOrder['id'])) {
                Log::error('Razorpay order response missing ID field', ['response' => $razorpayOrder]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment order. Missing order identifier.'
                ], 500);
            }
            
            Log::info('Razorpay order created successfully: ' . $razorpayOrder['id']);
            
            // Get Razorpay key ID for frontend
            $razorpayKeyId = DB::table('system_settings')->where('key', 'razorpay_key_id')->value('value');
            $razorpayKeyId = trim($razorpayKeyId ?? '');

            return response()->json([
                'success' => true,
                'order_id' => $razorpayOrder['id'],
                'amount' => $amount,
                'currency' => 'INR',
                'razorpay_key_id' => $razorpayKeyId
            ]);
        } catch (\Razorpay\Api\Errors\Error $e) {
            Log::error('Razorpay API Error: ' . $e->getMessage() . ' (Error Code: ' . $e->getCode() . ')');
            
            // Check if it's an amount limit error
            if (strpos($e->getMessage(), 'Amount exceeds maximum amount allowed') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount exceeds the maximum allowed limit. Please contact support.'
                ], 400);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Payment service temporarily unavailable. Please try again later.'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Razorpay Order Creation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order. Please try again.'
            ], 500);
        }
    }

    /**
     * Create a payment order for movie creation
     */
    public function createMoviePaymentOrder(Request $request)
    {
        try {
            Log::info('Creating movie payment order');
            
            // Get Razorpay API instance
            $api = $this->getRazorpayApi();
            
            // Check if Razorpay API is initialized
            if (!$api) {
                Log::error('Razorpay API not initialized for movie payment');
                return response()->json([
                    'success' => false,
                    'message' => 'Payment gateway not properly configured. Please ensure Razorpay API keys are set in the admin settings.'
                ], 500);
            }
            
            // Get casting director payment amount based on movie budget
            // We'll get the budget from the request if available
            $budget = $request->input('budget');
            $amount = calculate_casting_director_payment($budget);
            
            Log::info('Movie payment amount calculated: ' . $amount . ' (based on budget: ' . ($budget ?? 'N/A') . ')');
            
            if ($amount <= 0) {
                Log::warning('Invalid movie payment amount: ' . $amount);
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount not configured properly.'
                ], 400);
            }
            
            // Convert amount to paise (smallest currency unit)
            $amountInPaise = $amount * 100;
            Log::info('Amount in paise: ' . $amountInPaise);
            
            // Create Razorpay order
            $orderData = [
                'receipt'         => 'movie_' . time(),
                'amount'          => $amountInPaise, // Amount in paise
                'currency'        => 'INR',
                'payment_capture' => 1 // Auto-capture payment
            ];
            
            Log::info('Creating Razorpay order with data: ', $orderData);

            // Add timeout and error handling
            $razorpayOrder = null;
            try {
                $razorpayOrder = $api->order->create($orderData);
                Log::info('Raw Razorpay order response: ', ['response' => $razorpayOrder]);
            } catch (\Exception $e) {
                Log::error('Exception during Razorpay order creation: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e; // Re-throw to be caught by outer catch block
            }
            
            // Check if order was created successfully
            if (!$razorpayOrder) {
                Log::error('Razorpay order creation returned null');
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment order. Service unavailable.'
                ], 500);
            }
            
            // Ensure we have the expected structure
            if (!is_array($razorpayOrder) && !($razorpayOrder instanceof \ArrayAccess)) {
                Log::error('Razorpay order response is not array-like', ['type' => gettype($razorpayOrder)]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment order. Invalid response format.'
                ], 500);
            }
            
            if (!isset($razorpayOrder['id'])) {
                Log::error('Razorpay order response missing ID field', ['response' => $razorpayOrder]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment order. Missing order identifier.'
                ], 500);
            }
            
            Log::info('Razorpay order created successfully: ' . $razorpayOrder['id']);
            
            // Get Razorpay key ID for frontend
            $razorpayKeyId = DB::table('system_settings')->where('key', 'razorpay_key_id')->value('value');
            $razorpayKeyId = trim($razorpayKeyId ?? '');

            return response()->json([
                'success' => true,
                'order_id' => $razorpayOrder['id'],
                'amount' => $amount,
                'currency' => 'INR',
                'razorpay_key_id' => $razorpayKeyId
            ]);
        } catch (\Razorpay\Api\Errors\Error $e) {
            Log::error('Razorpay API Error: ' . $e->getMessage() . ' (Error Code: ' . $e->getCode() . ')');
            
            // Check if it's an amount limit error
            if (strpos($e->getMessage(), 'Amount exceeds maximum amount allowed') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount exceeds the maximum allowed limit. Please contact support.'
                ], 400);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Payment service temporarily unavailable. Please try again later.'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Razorpay Order Creation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify payment and process audition submission
     */
    public function verifyAuditionPaymentAndSubmit(Request $request)
    {
        try {
            // Get Razorpay API instance
            $api = $this->getRazorpayApi();
            
            // Check if Razorpay API is initialized
            if (!$api) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment gateway not properly configured. Please contact administrator.'
                ], 500);
            }
            
            // Validate payment details
            $request->validate([
                'razorpay_payment_id' => 'required|string',
                'razorpay_order_id' => 'required|string',
                'razorpay_signature' => 'required|string',
            ]);

            // Verify payment signature
            $attributes = [
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // Payment verified successfully
            // Now we can proceed with audition submission
            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully. You can now submit your audition.'
            ]);
        } catch (\Razorpay\Api\Errors\Error $e) {
            Log::error('Razorpay API Error: ' . $e->getMessage() . ' (Error Code: ' . $e->getCode() . ')');
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed. Please try again.'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Razorpay Payment Verification Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed. Please try again.'
            ], 400);
        }
    }

    /**
     * Verify payment and process movie creation
     */
    public function verifyMoviePaymentAndCreate(Request $request)
    {
        try {
            // Get Razorpay API instance
            $api = $this->getRazorpayApi();
            
            // Check if Razorpay API is initialized
            if (!$api) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment gateway not properly configured. Please contact administrator.'
                ], 500);
            }
            
            // Validate payment details
            $request->validate([
                'razorpay_payment_id' => 'required|string',
                'razorpay_order_id' => 'required|string',
                'razorpay_signature' => 'required|string',
            ]);

            // Verify payment signature
            $attributes = [
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // Payment verified successfully
            // Now we can proceed with movie creation
            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully. You can now create the movie.'
            ]);
        } catch (\Razorpay\Api\Errors\Error $e) {
            Log::error('Razorpay API Error: ' . $e->getMessage() . ' (Error Code: ' . $e->getCode() . ')');
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed. Please try again.'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Razorpay Payment Verification Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed. Please try again.'
            ], 400);
        }
    }

    /**
     * Get audition user payment amount from settings
     */
    private function getAuditionUserAmount()
    {
        try {
            $setting = SystemSetting::where('key', 'audition_user_amount')->first();
            $amount = $setting ? $setting->value : null;
            $amount = trim($amount ?? '');
            
            Log::info('Retrieved audition user amount', [
                'raw_value' => $amount,
                'is_numeric' => is_numeric($amount),
                'final_amount' => $amount ? (float)$amount : 0
            ]);
            
            return $amount && is_numeric($amount) ? (float)$amount : 0;
        } catch (\Exception $e) {
            Log::error('Error retrieving audition user amount: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get casting director payment amount from settings
     */
    private function getCastingDirectorAmount()
    {
        try {
            // First check for fixed amount
            $setting = SystemSetting::where('key', 'casting_director_amount')->first();
            $fixedAmount = $setting ? $setting->value : null;
            $fixedAmount = trim($fixedAmount ?? '');
            
            Log::info('Retrieved casting director amount', [
                'raw_value' => $fixedAmount,
                'is_numeric' => is_numeric($fixedAmount),
                'final_amount' => $fixedAmount ? (float)$fixedAmount : 0
            ]);
            
            if ($fixedAmount && is_numeric($fixedAmount)) {
                return (float)$fixedAmount;
            }
            
            // If no fixed amount, return 0
            return 0;
        } catch (\Exception $e) {
            Log::error('Error retrieving casting director amount: ' . $e->getMessage());
            return 0;
        }
    }
}