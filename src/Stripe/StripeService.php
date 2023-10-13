<?php

namespace App\Stripe;

use App\Entity\Purchase;

class StripeService
{
    protected $secretKey;
    protected $publicKey;

    // * les valeurs $secretKey et $publictKey je les ai mis dans le service.yaml
    public function __construct(string $secretKey, string $publicKey) 
    {
        $this->secretKey = $secretKey;
        $this->publicKey = $publicKey;
    }

    // * pour recuperer la publicKey de service.yaml au fishier twig on le passant par le controller PurchasePaymentController qui render la page de payment
    public function getPublicKey(): string 
    {
        return $this->publicKey;  
    }

    public function getpaymentIntent(Purchase $purchase)
    {

       \Stripe\Stripe::setApiKey($this->secretKey);
       
       return \Stripe\PaymentIntent::create([
            'amount' => $purchase->getTotal(),
            'currency' => 'eur'
       ]);
    }
}