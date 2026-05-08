<?php

namespace App\Services;

use YooKassa\Client;
use Illuminate\Support\Facades\Log;

class YooKassaService
{
    protected $client;
    
    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuth(config('yookassa.shop_id'), config('yookassa.secret_key'));
    }
    
    /**
     * create payment
     */
    public function createPayment($amount, $description, $paymentId, $userEmail, $returnUrl)
    {
        try {
            $payment = $this->client->createPayment(
                [
                    'amount' => [
                        'value' => (string)$amount,
                        'currency' => 'RUB',
                    ],
                    'confirmation' => [
                        'type' => 'redirect',
                        'return_url' => $returnUrl,
                    ],
                    'capture' => true,
                    'description' => $description,
                    'metadata' => [
                        'payment_id' => $paymentId,
                        'user_id' => auth()->id(),
                    ],
                ],
                $paymentId
            );
            
            return $payment;
            
        } catch (\Exception $e) {
            Log::error('YooKassa payment creation failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * get information for payment
     */
    public function getPayment($paymentId)
    {
        try {
            return $this->client->getPaymentInfo($paymentId);
        } catch (\Exception $e) {
            Log::error('YooKassa get payment failed: ' . $e->getMessage());
            throw $e;
        }
    }
}