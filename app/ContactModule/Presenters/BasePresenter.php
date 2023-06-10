<?php
declare(strict_types=1);

namespace App\ContactModule\Presenters;

use Doctrine\ORM\EntityManagerInterface;
use Nette\Localization\Translator;

class BasePresenter extends \App\Presenters\BasePresenter
{
    /** @var EntityManagerInterface @inject */
    public $em;

    /** @var Translator @inject */
    public $translator;
}