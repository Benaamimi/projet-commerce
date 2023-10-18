<?php

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;
    protected $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger =$logger;
        $this->mailer =$mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendEmail'
        ];
    }

    public function sendEmail(ProductViewEvent $productViewEvent)
    {
        // $email = new TemplatedEmail();
        // $email->from(new Address("contact@gmail.com", "Info de la boutique"))
        // ->to("admin@gmail.com")
        // ->text("Un visiteur est en train de voir la page du produit n°" . $productViewEvent->getProduct()->getId())
        // ->htmlTemplate('emails/product_view.html.twig')
        // ->context([
        //     'product' => $productViewEvent->getProduct() //* cibler le produit concerné (twig)
        // ])
        // ->subject("Visite du produit n°" . $productViewEvent->getProduct()->getId());

        // $this->mailer->send($email);

        $this->logger->info("Email envoyé pour le produit n°" . $productViewEvent->getProduct()->getId());
    } 
}