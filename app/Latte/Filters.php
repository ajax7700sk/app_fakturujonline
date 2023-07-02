<?php

namespace App\Latte;

use App\Entity\FlexibeeLog;
use App\Entity\MediaObject;
use App\Entity\Transport;
use App\Service\FileService;
use Latte\Engine;

/**
 * Class Filters - App global Latte filters
 *
 * @package App\Latte
 */
class Filters
{
    /** @var \Nette\DI\Container @inject */
    public $container;

    /** @var Engine */
    private $latteEngine;


    public function moneyFormat($value, string $currencyCode, string $localeCode): string
    {
        return $value;
    }

    public function transPaymentMethod(string $value): string
    {
        switch ($value) {
            case 'bank_payment':
                return 'Bankový prevod';
            case 'cash_on_delivery':
                return 'Dobierka';
            case 'cash':
                return 'Hotovosť';
            case 'paypal':
                return 'PayPal';
            case 'card':
                return 'Platobná karta';
        }
    }
}
