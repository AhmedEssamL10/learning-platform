<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class PaymobService
{
    protected $apiKey;
    protected $baseUrl;
    protected $integrationId;
    protected $headers;
    public function __construct()
    {
        $this->apiKey = env('PAYMOB_API_KEY');
        $this->baseUrl = env('PAYMOB_BASE_URL');
        $this->integrationId = env('PAYMOB_INTEGRATION_ID');
        $this->headers = [
            'Content-Type: application/json',
        ];
    }
    public function authenticate()
    {
        $response = Http::post("{$this->baseUrl}/auth/tokens", [
            'api_key' => $this->apiKey,
        ]);
        if ($response->successful()) {
            return response($response->json()['token']);
        }
        return response()->json(['error' => 'Authentication failed'], 500);
    }
    public function createOrder($authToken, $amount, $currency = 'EGP')
    {
        $response = Http::post("{$this->baseUrl}/ecommerce/orders", [
            'auth_token' => $authToken,
            'delivery_needed' => false,
            "api_source" => "INVOICE",
            'amount_cents' => $amount * 100,
            'currency' => $currency,
            'items' => [],
        ]);
        if ($response->successful()) {
            return $response->json();
        }
        return response()->json(['error' => 'Order creation failed'], 500);
    }
    public function verifyCallback($data)
    {
        $hmacSecret = env('PAYMOB_HMAC');

        // Concatenate the required fields
        $string = $data['amount_cents'] .
            $data['created_at'] .
            $data['currency'] .
            $data['error_occured'] .
            $data['has_parent_transaction'] .
            $data['id'] .
            $data['integration_id'] .
            $data['is_3d_secure'] .
            $data['is_auth'] .
            $data['is_capture'] .
            $data['is_refunded'] .
            $data['is_standalone_payment'] .
            $data['is_voided'] .
            $data['order'] .
            $data['owner'] .
            $data['pending'] .
            $data['source_data_pan'] .
            $data['source_data_sub_type'] .
            $data['source_data_type'] .
            $data['success'];

        $calculatedHmac = hash_hmac('sha512', $string, $hmacSecret);

        return $calculatedHmac === $data['hmac'];
    }
}