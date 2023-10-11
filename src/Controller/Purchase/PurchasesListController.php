<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PurchasesListController extends AbstractController    
{
    #[Route('/purchases', name:"purchase_index")]
    #[IsGranted("ROLE_USER", message:"Vous devez être connecté(e) pour accéder à vos commandes")]
    public function index(): Response
    {
        // 1. Nous devons nous asssurer que la personne est connectée (sinon redirection vers la page d'accueil) -> Security
       /** @var User */  //? précise que $user est une variable de user 
        $user = $this->getUser();  

        // todo IsGranted en annotation fait la même chose que la fonction (raccoucis) 
        // todo avec cette fonction je peux afficher le message addFlash 
        // if(!$user){
            // 1. Redirection -> RedirectResponse
            // 2. Générer une url en fonction du nom d'une route -> UrlGeneratorIterface ou RouterInterface
            //* $url = $this->router->generate("homepage");
            //* return new RedirectResponse($url);  
            // 3. encore mieux
            //$this->addFlash("warning", "Vous devez être connecté(e) pour accéder à vos commandes");
            //throw new AccessDeniedException();    
        // }

        // 2. Nous devons savoir QUI est connecté -> Security
        // 3. Nous voulons passer l'utilisateur connecté à twig afin d'afficher ses commandes -> Evironment de twig / Response
        return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
        ]);
    }
}

