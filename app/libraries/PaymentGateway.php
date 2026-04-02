<?php
/**
 * Abstract Payment Gateway
 * Base class for all payment gateway implementations
 */
abstract class PaymentGateway
{
    protected $config;
    
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->initialize();
    }
    
    /**
     * Initialize the payment gateway
     */
    abstract protected function initialize();
    
    /**
     * Create a payment intent/order
     * 
     * @param float $amount Amount in smallest currency unit (e.g., cents)
     * @param array $metadata Additional data (customer info, order details, etc.)
     * @return array Payment intent data including client_secret
     */
    abstract public function createPaymentIntent($amount, array $metadata = []);
    
    /**
     * Confirm/capture a payment
     * 
     * @param string $paymentIntentId Payment intent ID
     * @return array Payment confirmation data
     */
    abstract public function confirmPayment($paymentIntentId);
    
    /**
     * Handle webhook events
     * 
     * @param string $payload Raw webhook payload
     * @param string $signature Webhook signature for verification
     * @return array Processed event data
     */
    abstract public function handleWebhook($payload, $signature);
    
    /**
     * Get the publishable/public key for client-side integration
     * 
     * @return string Public API key
     */
    abstract public function getPublicKey();
    
    /**
     * Refund a payment
     * 
     * @param string $paymentIntentId Payment intent ID
     * @param float $amount Amount to refund (null for full refund)
     * @return array Refund data
     */
    abstract public function refundPayment($paymentIntentId, $amount = null);
    
    /**
     * Get payment status
     * 
     * @param string $paymentIntentId Payment intent ID
     * @return array Payment status data
     */
    abstract public function getPaymentStatus($paymentIntentId);
}
?>
