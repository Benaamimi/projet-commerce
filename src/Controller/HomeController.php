<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    // #[Route('/', name: "homepage")]

    /**
     * @Route("/", name="homepage")
     */
    public function homepage(ProductRepository $repo) 
    //avec product repositiry je me fais livrer livre les produit demander
    {
        $product = $repo->findBy([], [], 3);
    
    
        return $this->render("home.html.twig", [
            'products' => $product
        ]);
    }


}