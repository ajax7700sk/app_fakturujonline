<?php
declare(strict_types=1);

namespace App\Intl;

class Currencies
{
    public static function getNames(): array
    {
        return array(
            'EUR' => 'Euro',
            'CZK' => 'Česká koruna'
        );
    }
}