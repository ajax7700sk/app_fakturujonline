<?php
declare(strict_types=1);

namespace App\Intl;

class Countries
{
    public static function getNames(): array
    {
        return array(
            'SK' => 'Slovensko',
            'CZ' => 'Česko'
        );
    }
}