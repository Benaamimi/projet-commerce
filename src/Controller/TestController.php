<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

Class TestController extends AbstractController
{

    
    /**
     * @route("/", name="index")
     */
    public function index()
    {
        var_dump("ca fontionne");
        die();
    }

    /**
     * @Route("/test/{age<\d+>?0}", name="test", methods={"GET", "POST"}, host="127.0.0.1", schemes={"http", "https"})
     */
    public function test(Request $request, $age)
    {
        // $request= Request::createFromGlobals();
        //en peut remplacer cette ligne avec ce qu'il ya sur le parametre de la fonction test

        // $age = $request->attributes->get('age');
        //en peut remplacer cette ligne avec ce qu'il ya sur le parametre de la fonction test


       return new Response("Vous avez $age ans.");  
    }

  





}