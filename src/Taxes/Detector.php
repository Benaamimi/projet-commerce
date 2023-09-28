<?php

namespace App\Taxes;


class Detector
{
    protected $plafond;

    public function __construct(float $plafond)
    {
        $this->plafond = $plafond;
        
    } 


    public function detect(float $amount) : bool
    {
        if($amount > $this->plafond){
            return true;
        }
           return false;

    }
}
