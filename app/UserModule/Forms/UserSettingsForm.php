<?php
declare(strict_types=1);

namespace App\UserModule\Forms;

use App\Forms\AbstractForm;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;
use Nette\Localization\Translator;
use Nette\Security\User;

class UserSettingsForm extends AbstractForm
{
    private EntityManagerInterface $entityManager;

    /** @var \Nette\Security\User */
    public $securityUser;
    private Translator $translator;
    /** @var \App\Entity\User */
    private $user;

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
        $this->template->render(__DIR__.'./../templates/forms/user-settings.latte');
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

        $form->addHidden('id', 'ID');
        $form->addText('firstName', 'Meno')
             ->setRequired("Pole je povinné");
        $form->addText('lastName', 'Meno')
             ->setRequired("Pole je povinné");
        $form->addText('phoneNumber', 'Telefón');

        //
        $form->addSubmit("submit", 'form.general.submit.label');

        // Defaults
        if($this->user) {
            $this->setDefaults($form);
        }

        // Events
        $form->onValidate[] = [$this, 'onValidate'];
        $form->onSuccess[]  = [$this, 'onSuccess'];

        return $form;
    }

    public function onValidate(Form $form): void
    {
        //
    }

    public function onSuccess(Form $form): void
    {
        /** @var \Nette\Utils\ArrayHash $values */
        $values = $form->getValues(true);

        $user = $this->user;
        $user->setFirstName($values['firstName']);
        $user->setLastName($values['lastName']);
        $user->setPhoneNumber($values['phoneNumber']);

        //
        $this->entityManager->flush();

        // Redirect to dashboard
        $this->presenter->flashMessage('Nastavenia boli úspešne uložené', 'success');
        $this->presenter->redirect(':User:Settings:default');
    }

    // ------------------------------------ Helpers ---------------------------------- \\

    public function setUser(\App\Entity\User $user): void
    {
        $this->user = $user;
    }

    private function setDefaults(Form $form): void
    {
        $defaults = array(
            'firstName' => $this->user->getFirstName(),
            'lastName' => $this->user->getLastName(),
            'phoneNumber' => $this->user->getPhoneNumber()
        );

        $form->setDefaults($defaults);
    }

}

/**
 *
 */
interface IUserSettingsForm
{
    public function create(): UserSettingsForm;
}