<?php
declare(strict_types=1);

namespace App\Forms;

use Nette\Application\UI\Control;
use Nette\Localization\ITranslator;

abstract class AbstractForm extends Control
{
    /** @var ITranslator @inject */
    public $translator;
}