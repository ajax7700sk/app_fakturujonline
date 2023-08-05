<?php
declare(strict_types=1);

namespace App\Forms;

use Nette\Application\UI\Control;
use Nette\Localization\ITranslator;
use Nette\Security\User;

abstract class AbstractForm extends Control
{
    protected function checkboxValue($value): bool
    {
        if($value == 'on') {
            return true;
        } else {
            return false;
        }
    }
}