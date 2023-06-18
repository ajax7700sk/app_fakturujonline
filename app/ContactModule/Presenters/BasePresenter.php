<?php
declare(strict_types=1);

namespace App\ContactModule\Presenters;

use App\ContactModule\Forms\ContactForm;
use App\ContactModule\Forms\IContactForm;
use App\SecurityModule\Forms\LoginForm;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Localization\Translator;

class BasePresenter extends \App\Presenters\BasePresenter
{
    /** @var EntityManagerInterface @inject */
    public $em;

    /** @var Translator @inject */
    public $translator;

    /** @var IContactForm @inject */
    public $contactForm;

    /*********************************************************************
     * Components
     ********************************************************************/

    public function createComponentContactForm(): ContactForm
    {
        /** @var ContactForm $control */
        $control = $this->contactForm->create();

        return $control;
    }
}