<?php

namespace App\Controller;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelloController extends AbstractController
{
    /**
     * @Route("/hello/{name?World}", name="hello")
     */
    public function hello($name = "World")//on peut aussi le parametrer ici !
    //cette fonction traite une requette http et renvoie une reponse http
    {
       
       return $this->render('hello.html.twig', [
        'prenom' => $name
       ]);
    }

    /**
     * @Route("/example", name="example")
     */
    public function example()
    {
       return $this->render('example.html.twig', [
        'age' => 40
       ]);
    }
}
