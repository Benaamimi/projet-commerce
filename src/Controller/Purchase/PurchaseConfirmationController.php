<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class PurchaseConfirmationController extends AbstractController
{
    #[Route('/purchase/confirm', name:"purchase_confirm")]
    #[IsGranted("ROLE_USER", message:"Vous devez être connecter pour confirmer une commande")]
    public function confirm(Request $request, PurchasePersister $persister, CartService $cartService) 
    //* on ne peut pas demander la request dans le __construt, car elle est lié à la route, donc elle est unique (changante)
    {
        // 1. Nous voulons lire les données du formulaire
        // FormFactoryInterface/Request
        $form = $this->createForm(CartConfirmationType::class);

        $form->handleRequest($request); //? analyse la Request

        // 2. Si le formulaire n'a pas été soumis : dégager
        if(!$form->isSubmitted()){
            // message Flash puis redirection (FlashBagIinterface)
            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');

            return $this->redirectToRoute('cart_show');
        }
        
        // 3. Si je ne suis pas connecté : dégager (Security)
        // * #[IsGranted("ROLE_USER")]
        // ou
        // * $user = $this->getUser();

        // 4. Si il y a pas de produits dans le panier : dégager (CartService où SessionInterface)
        $cartItems = $cartService->getDetailedcartItems();

        if(count($cartItems) === 0){ //? si il y a pas dans cartItems de item (si le compte === 0) panier vide
            $this->addFlash('warning', 'Vous ne pouvez pas confirmer la commande avec un panier vide');

            return $this->redirectToRoute("cart_show");   
        }

        // 5. Nous allons céer une purchase
        /** @var Purchase */
        $purchase = $form->getData(); // recupérer la purchase (la commande) de mon formulaire
        //* c'est un tableau assotiatif à la base, mais quand on ajoute l'option 'data_class' => Purchase::class dans la fonction d'option dans le CartConfirmationType il deviens une class de l'entity Purchase
     
        $persister->storePurchase($purchase); // je persist avec PurchasePersister $persister que j'ai créer
     
        return $this->redirectToRoute('purchase_payment_form', [
            'id' => $purchase->getId()
        ]);
    }
}