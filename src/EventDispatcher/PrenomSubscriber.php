<?php

namespace App\EventDispatcher;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class PrenomSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
       return [
        'kernel.request' => 'addPrenomToAttributes', // au moment ou on reçoi la requette
        // 'kernel.controller' => 'test1', // au moment ou on vas dans le controller 
        // 'kernel.response' => 'test2' // au moment de la response
       ];
    }

    public function addPrenomToAttributes(RequestEvent $requestEvent)
    {
       $requestEvent->getRequest()->attributes->set('prenom', 'Tarik'); // Ajouter un attribut a la requette
    }

    // public function test1()
    // {
    //     dump("test1");
    // }

    // public function test2()
    // {
    //     dump("test2");
    // }
    
}