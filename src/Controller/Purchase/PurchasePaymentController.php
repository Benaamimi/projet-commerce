<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Stripe\StripeService;
use App\Repository\PurchaseRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController
{
    #[Route("/purchase/pay/{id}", name:"purchase_payment_form")]
    #[IsGranted("ROLE_USER")]
    public function showCardForm($id, PurchaseRepository $purchaseRepository, StripeService $stripeService): Response
    {
        $purchase = $purchaseRepository->find($id);

        if(!$purchase || ($purchase && $purchase->getUser() !== $this->getUser()) || ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)){
            return $this->redirectToRoute('cart_show');
        }
        
        $intent = $stripeService->getpaymentIntent($purchase);

        $clientSecret = $intent->client_secret;
        return $this->render('purchase/payment.html.twig', [
            'clientSecret' =>  $clientSecret,
            'purchase' => $purchase,
            'stripePublicKey' => $stripeService->getPublicKey() // c'est la publicKey qui est stocker dans StripeService dans le contrutor qui lui même la recupère de service.yaml ou j'ai stocker la le code ou la clé 
        ]);
    }
}