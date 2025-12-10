<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseAPIController as Controller;
use Illuminate\Http\Request;
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
                $api = new \Razorpay\Api\Api($key, $secret);
                return $api;
            } else {
                Log::warning('Razorpay keys not properly configured', [
                    'key_id_present' => !empty($key),
                    'key_secret_present' => !empty($secret)
                ]);
                
                if (empty($key)) {
                    Log::warning('Razorpay Key ID is missing or invalid');
                }
                
                if (empty($secret)) {
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
                return $this->sendError('Payment gateway not properly configured. Please ensure Razorpay API keys are set in the admin settings.', [], 500);
            }
            
            // Get audition payment amount from settings
            $amount = $this->getAuditionUserAmount();
            
            // Check if amount is greater than 0
            if ($amount <= 0) {
                return $this->sendError('Payment is not required at this time.', [], 400);
            }
            
            // Create Razorpay order
            $razorpayOrder = $api->order->create([
                'receipt' => 'order_rcptid_' . time(),
                'amount' => $amount * 100, // Amount in paise
                'currency' => 'INR',
                'payment_capture' => 1 // Auto-capture payment
            ]);
            
            // Check if order ID exists in response
            if (!isset($razorpayOrder['id'])) {
                Log::error('Razorpay order response missing ID field', ['response' => $razorpayOrder]);
                return $this->sendError('Failed to create payment order. Missing order identifier.', [], 500);
            }
            
            Log::info('Razorpay order created successfully: ' . $razorpayOrder['id']);
            
            // Get Razorpay key ID for frontend
            $razorpayKeyId = DB::table('system_settings')->where('key', 'razorpay_key_id')->value('value');
            $razorpayKeyId = trim($razorpayKeyId ?? '');

            return $this->sendResponse([
                'order_id' => $razorpayOrder['id'],
                'amount' => $amount,
                'currency' => 'INR',
                'razorpay_key_id' => $razorpayKeyId
            ], 'Payment order created successfully.');
        } catch (\Razorpay\Api\Errors\Error $e) {
            Log::error('Razorpay API Error: ' . $e->getMessage() . ' (Error Code: ' . $e->getCode() . ')');
            
            // Check if it's an amount limit error
            if (strpos($e->getMessage(), 'Amount exceeds maximum amount allowed') !== false) {
                return $this->sendError('Payment amount exceeds the maximum allowed limit. Please contact support.', [], 400);
            }
            
            return $this->sendError('Payment service temporarily unavailable. Please try again later.', [], 500);
        } catch (\Exception $e) {
            Log::error('Razorpay Order Creation Error: ' . $e->getMessage());
            return $this->sendError('Failed to create payment order. Please try again.', [], 500);
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
                return $this->sendError('Payment gateway not properly configured. Please ensure Razorpay API keys are set in the admin settings.', [], 500);
            }
            
            // Get casting director payment amount based on movie budget
            // We'll get the budget from the request if available
            $budget = $request->input('budget');
            $amount = calculate_casting_director_payment($budget);
            
            Log::info('Movie payment amount calculated: ' . $amount . ' (based on budget: ' . ($budget ?? 'N/A') . ')');
            
            // Check if amount is greater than 0
            if ($amount <= 0) {
                return $this->sendError('Payment is not required at this time.', [], 400);
            }
            
            // Create Razorpay order
            $razorpayOrder = $api->order->create([
                'receipt' => 'movie_order_rcptid_' . time(),
                'amount' => $amount * 100, // Amount in paise
                'currency' => 'INR',
                'payment_capture' => 1 // Auto-capture payment
            ]);
            
            // Check if order ID exists in response
            if (!isset($razorpayOrder['id'])) {
                Log::error('Razorpay order response missing ID field', ['response' => $razorpayOrder]);
                return $this->sendError('Failed to create payment order. Missing order identifier.', [], 500);
            }
            
            Log::info('Razorpay order created successfully: ' . $razorpayOrder['id']);
            
            // Get Razorpay key ID for frontend
            $razorpayKeyId = DB::table('system_settings')->where('key', 'razorpay_key_id')->value('value');
            $razorpayKeyId = trim($razorpayKeyId ?? '');

            return $this->sendResponse([
                'order_id' => $razorpayOrder['id'],
                'amount' => $amount,
                'currency' => 'INR',
                'razorpay_key_id' => $razorpayKeyId
            ], 'Payment order created successfully.');
        } catch (\Razorpay\Api\Errors\Error $e) {
            Log::error('Razorpay API Error: ' . $e->getMessage() . ' (Error Code: ' . $e->getCode() . ')');
            
            // Check if it's an amount limit error
            if (strpos($e->getMessage(), 'Amount exceeds maximum amount allowed') !== false) {
                return $this->sendError('Payment amount exceeds the maximum allowed limit. Please contact support.', [], 400);
            }
            
            return $this->sendError('Payment service temporarily unavailable. Please try again later.', [], 500);
        } catch (\Exception $e) {
            Log::error('Razorpay Order Creation Error: ' . $e->getMessage());
            return $this->sendError('Failed to create payment order. Please try again.', [], 500);
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
                return $this->sendError('Payment gateway not properly configured. Please contact administrator.', [], 500);
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
            return $this->sendResponse([], 'Payment verified successfully. You can now submit your audition.');
        } catch (\Razorpay\Api\Errors\Error $e) {
            Log::error('Razorpay API Error: ' . $e->getMessage() . ' (Error Code: ' . $e->getCode() . ')');
            return $this->sendError('Payment verification failed. Please try again.', [], 400);
        } catch (\Exception $e) {
            Log::error('Razorpay Payment Verification Error: ' . $e->getMessage());
            return $this->sendError('Payment verification failed. Please try again.', [], 400);
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
                return $this->sendError('Payment gateway not properly configured. Please contact administrator.', [], 500);
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
            return $this->sendResponse([], 'Payment verified successfully. You can now create the movie.');
        } catch (\Razorpay\Api\Errors\Error $e) {
            Log::error('Razorpay API Error: ' . $e->getMessage() . ' (Error Code: ' . $e->getCode() . ')');
            return $this->sendError('Payment verification failed. Please try again.', [], 400);
        } catch (\Exception $e) {
            Log::error('Razorpay Payment Verification Error: ' . $e->getMessage());
            return $this->sendError('Payment verification failed. Please try again.', [], 400);
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