<?php
declare(strict_types=1);

namespace App\UserModule\Forms;

use App\Forms\AbstractForm;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;
use Nette\Localization\Translator;
use Nette\Security\Passwords;
use Nette\Security\User;

class PasswordChangeForm extends AbstractForm
{
    private EntityManagerInterface $entityManager;

    /** @var \Nette\Security\User */
    public $securityUser;
    private Translator $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        Translator $translator,
        User $securityUser
    ) {
        $this->entityManager = $entityManager;
        $this->translator    = $translator;
        $this->securityUser  = $securityUser;
    }

    /**
     * Render a form
     */
    public function render()
    {
        // Render
        $this->template->render(__DIR__.'/../templates/forms/password-change.latte');
    }

    /*********************************************************************
     * Component form
     ********************************************************************/

    /**
     * Create a form
     *
     * @return \Nette\Application\UI\Form
     */
    public function createComponentForm()
    {
        $form = new Form();
        $form->getElementPrototype()
             ->setAttribute('novalidate', "novalidate");
        $form->setTranslator($this->translator);

        //
        $form->addPassword('passwordOld', 'Aktuálne heslo')
             ->setRequired("Pole je povinné");
        $form->addPassword('passwordNew', 'Nové heslo')
             ->setRequired("Pole je povinné");
        $form->addPassword('passwordNewRepeat', 'Nové heslo znova')
             ->setRequired("Pole je povinné");

        //
        $form->addSubmit("submit", 'form.general.submit.label');
        //
        $this->setDefaults($form);

        // Events
        $form->onValidate[] = [$this, 'onValidate'];
        $form->onSuccess[]  = [$this, 'onSuccess'];

        return $form;
    }

    public function onValidate(Form $form): void
    {
        $values    = $form->getValues(true);
        $passwords = new Passwords();
        $user      = $this->getLoggedUser();
        // Check if old password is valid
        $oldPassword = $values['passwordOld'];

        if (!$passwords->verify($oldPassword, $user->getPassword())) {
            $form->addError('Zadané pôvodné heslo nie je správne');

            return;
        }

        // Check if new password is same as repeated
        if ($values['passwordNew'] != $values['passwordNewRepeat']) {
            $form->addError('Zadané nové heslá sa nezhodujú');

            return;
        }
    }

    public function onSuccess(Form $form): void
    {
        /** @var \Nette\Utils\ArrayHash $values */
        $values = $form->getValues(true);

        // ------------------------------------- User company ---------------------------------------- \\
        $passwords = new Passwords();

        $user = $this->getLoggedUser();
        $user->setPassword($passwords->hash($values['passwordNew']));
        //
        $this->entityManager->flush();

        $this->presenter->flashMessage('Heslo bolo úspešne zmenené', 'success');
        // Redirect
        $this->presenter->redirect(':User:Settings:default');
    }

    // ------------------------------------ Helpers ---------------------------------- \\

    private function setDefaults(Form $form): void
    {
        $defaults = array();

        //
        $form->setDefaults($defaults);
    }

    private function getLoggedUser(): ?\App\Entity\User
    {
        /** @var \App\Entity\User|null $user */
        $user = $this->entityManager
            ->getRepository(\App\Entity\User::class)
            ->find((int)$this->securityUser->id);

        return $user;
    }
}

/**
 * Interface IContactForm
 *
 * @package App\Forms
 */
interface IPasswordChangeForm
{
    public function create(): PasswordChangeForm;
}