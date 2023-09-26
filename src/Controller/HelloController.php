<?php

namespace App\Controller;

use Twig\Environment;
use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelloController extends AbstractController
{
    // protected $calculator;

    // public function __construct(Calculator $calculator)
    // {
    //     $this->calculator = $calculator;
    // }

    /**
     * @Route("/hello/{name?World}", name="hello")
     */
    public function hello($name, Calculator $calculator, Slugify $slugify, Environment $twig)//on peut aussi le parametrer ici !
    //cette fonction traite une requette http et renvoie une reponse http
    {
        dump($twig);
        dump($slugify->slugify("Hello World"));
        $tva = $calculator->calcul(100);
        dump($tva);

        return new Response("Hello $name!");
    }
}
