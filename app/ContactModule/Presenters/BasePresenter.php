<?php
declare(strict_types=1);

namespace App\ContactModule\Presenters;

use App\ContactModule\Forms\ContactForm;
use App\ContactModule\Forms\IContactForm;
use App\Entity\Contact;
use App\SecurityModule\Forms\LoginForm;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Localization\Translator;

class BasePresenter extends \App\Presenters\BasePresenter
{
    /** @var IContactForm @inject */
    public $contactForm;

    // --- Form fields
    /** @var Contact|null */
    protected $contactFormContact = null;

    /*********************************************************************
     * Components
     ********************************************************************/

    public function createComponentContactForm(): ContactForm
    {
        /** @var ContactForm $control */
        $control = $this->contactForm->create();
        //
        $control->setContact($this->contactFormContact);

        return $control;
    }
}