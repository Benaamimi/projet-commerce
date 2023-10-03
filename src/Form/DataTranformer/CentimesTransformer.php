<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class CentimesTransformer implements DataTransformerInterface
{
    public function transform($value) //Agit avant d'afficher la valeur dans formulaire elle multiplie par 100
    {
        if($value === null){
            return;
        }
        return $value / 100;
    
    }

    public function reverseTransform($value) //Agit au moment qu'on a soumis la valeur dans le formulaire elle divise par 100
    {
        if($value === null){
            return;
        }
        return $value * 100;
    }
}