<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AmountExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('amount', [$this, 'amount'])
        ];
    }

    public function amount($value, string $symbol = '€', string $decsep = ',', string $thousandsep = ' ')
    {
        // de 12345 à 123,45 €

        $finalValue = $value / 100;
        // 123.45

        $finalValue = number_format($finalValue, 2, $decsep, $thousandsep);
        // 123,45

        return $finalValue . ' ' . $symbol;
        // 123,45 €
    }
}