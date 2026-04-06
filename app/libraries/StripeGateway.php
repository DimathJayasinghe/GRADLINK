<?php
require_once __DIR__ . '/PaymentGateway.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

/**
 * Stripe Payment Gateway Implementation
 */
class StripeGateway extends PaymentGateway
{
    private $stripeSecretKey;
    private $stripePublicKey;
    private $webhookSecret;
    private $currency;
    
    protected function initialize()
    {
        // Load configuration from environment or passed config
        $this->stripeSecretKey = $this->config['secret_key'] ?? gl_env('STRIPE_SECRET_KEY');
        $this->stripePublicKey = $this->config['public_key'] ?? gl_env('STRIPE_PUBLISHABLE_KEY');
        $this->webhookSecret = $this->config['webhook_secret'] ?? gl_env('STRIPE_WEBHOOK_SECRET');
        $this->currency = $this->config['currency'] ?? gl_env('STRIPE_CURRENCY', 'lkr');
        
        if (empty($this->stripeSecretKey)) {
            throw new Exception('Stripe secret key is not configured');
        }
        
        // Set Stripe API key
        Stripe::setApiKey($this->stripeSecretKey);
    }
    
    /**
     * Create a Stripe payment intent
     */
    public function createPaymentIntent($amount, array $metadata = [])
    {
        try {
            // Convert amount to smallest currency unit (cents for most currencies)
            // For LKR, we'll keep it as is since it doesn't have subunits
            $amountInCents = (int)($amount * 100);
            
            $intentData = [
                'amount' => $amountInCents,
                'currency' => strtolower($this->currency),
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ];
            
            // Add metadata if provided
            if (!empty($metadata)) {
                $intentData['metadata'] = $metadata;
            }
            
            // Add description if provided
            if (isset($metadata['description'])) {
                $intentData['description'] = $metadata['description'];
            }
            
            // Add customer email if provided
            if (isset($metadata['receipt_email'])) {
                $intentData['receipt_email'] = $metadata['receipt_email'];
            }
            
            $paymentIntent = PaymentIntent::create($intentData);
            
            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $amount,
                'currency' => $this->currency
            ];
            
        } catch (\Exception $e) {
            error_log('Stripe Payment Intent Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Confirm a payment (usually handled automatically by Stripe)
     */
    public function confirmPayment($paymentIntentId)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            
            return [
                'success' => true,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount / 100,
                'payment_intent_id' => $paymentIntent->id
            ];
            
        } catch (\Exception $e) {
            error_log('Stripe Confirm Payment Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook($payload, $signature)
    {
        try {
            // Verify webhook signature
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                $this->webhookSecret
            );
            
            // Handle different event types
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    return [
                        'success' => true,
                        'event_type' => 'payment_succeeded',
                        'payment_intent_id' => $paymentIntent->id,
                        'amount' => $paymentIntent->amount / 100,
                        'metadata' => $paymentIntent->metadata
                    ];
                    
                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    return [
                        'success' => true,
                        'event_type' => 'payment_failed',
                        'payment_intent_id' => $paymentIntent->id,
                        'error_message' => $paymentIntent->last_payment_error->message ?? 'Payment failed'
                    ];
                    
                default:
                    return [
                        'success' => true,
                        'event_type' => $event->type,
                        'handled' => false
                    ];
            }
            
        } catch (SignatureVerificationException $e) {
            error_log('Webhook Signature Verification Failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Invalid signature'
            ];
        } catch (\Exception $e) {
            error_log('Webhook Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get Stripe publishable key
     */
    public function getPublicKey()
    {
        return $this->stripePublicKey;
    }
    
    /**
     * Refund a payment
     */
    public function refundPayment($paymentIntentId, $amount = null)
    {
        try {
            $refundData = ['payment_intent' => $paymentIntentId];
            
            if ($amount !== null) {
                $refundData['amount'] = (int)($amount * 100);
            }
            
            $refund = \Stripe\Refund::create($refundData);
            
            return [
                'success' => true,
                'refund_id' => $refund->id,
                'status' => $refund->status,
                'amount' => $refund->amount / 100
            ];
            
        } catch (\Exception $e) {
            error_log('Stripe Refund Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get payment status
     */
    public function getPaymentStatus($paymentIntentId)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            
            return [
                'success' => true,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency,
                'created' => $paymentIntent->created
            ];
            
        } catch (\Exception $e) {
            error_log('Stripe Get Status Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
?>
