<?php
declare(strict_types=1);

namespace App\Presenters;

use Doctrine\ORM\EntityManagerInterface;
use Nette\Localization\Translator;

abstract class ApplicationPresenter extends \Nette\Application\UI\Presenter
{
    /** @var EntityManagerInterface @inject */
    public $em;

    /** @var Translator @inject */
    public $translator;

}