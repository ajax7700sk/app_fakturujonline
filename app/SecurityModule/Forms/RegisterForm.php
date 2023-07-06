<?php
declare(strict_types=1);

namespace App\SecurityModule\Forms;

use App\Entity\User;
use App\Forms\AbstractForm;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;
use Nette\Localization\Translator;
use Nette\Security\Passwords;
use Nette\Security\User as SecurityUser;

/**
 * Class RegisterForm
 *
 * @package App\Forms
 */
final class RegisterForm extends AbstractForm
{
    private EntityManagerInterface $entityManager;

    /** @var \Nette\Security\User */
    private $securityUser;

    private SecurityService $securityService;
    private Translator $translator;

    public function __construct(
        Translator $translator,
        EntityManagerInterface $entityManager,
        SecurityService $securityService,
        SecurityUser $securityUser
    ) {
        $this->entityManager   = $entityManager;
        $this->securityService = $securityService;
        $this->securityUser    = $securityUser;
        $this->translator      = $translator;
    }

    /**
     * Render a form
     */
    public function render()
    {
        // Render
        $this->template->render(__DIR__.'./../templates/forms/register.latte');
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

        $form->addText('firstName', 'Meno')
             ->setRequired("Pole je povinné");
        $form->addText('lastName', 'Priezvisko')
             ->setRequired("Pole je povinné");
        $form->addText('email', 'E-mail')
             ->setRequired("Pole je povinné");
        $form->addPassword('password', 'Heslo')
             ->setRequired("form.validation.required");
        $form->addPassword('passwordRepeat', 'Heslo znova')
             ->setRequired("form.validation.required");

        $form->addSubmit("submit", 'form.general.submit.label');

        // Events
        $form->onValidate[] = [$this, 'onValidate'];
        $form->onSuccess[]  = [$this, 'onSuccess'];

        return $form;
    }

    public function onValidate(Form $form): void
    {
        $values = $form->getValues();
        // Check if email is used

        $repo = $this->entityManager->getRepository(User::class);
        /** @var User $user */
        $user = $repo->findOneBy([
            'email' => $values->email,
        ]);

        if ($user) {
            $form->addError('Užívateľ s týmto e-mailom už existuje');
        }
    }

    public function onSuccess(Form $form): void
    {
        /** @var \Nette\Utils\ArrayHash $values */
        $values = $form->getValues();

        try {
            $user = $this->createUserFromForm($form);
            $user = $this->securityService->registerUser($user);
            // Redirect to dashboard
            $this->presenter->flashMessage('Boli ste úspešne zaregistrovaný', 'success');
            $this->presenter->redirect(':Security:Auth:login');
        } catch (\Nette\Security\AuthenticationException $e) {
            $this->presenter->flashMessage("form.login.validation.authentication");
            $this->presenter->redirect("this");
        }
    }

    // ------------------------------ Helpers

    private function createUserFromForm(Form $form): User
    {
        $passwords = new Passwords();
        //
        $values = $form->getValues();

        $user = new User();
        $user->setEmail($values->email);
        $user->setFirstName($values->firstName);
        $user->setLastName($values->lastName);
        $user->setPassword($passwords->hash($values->password));

        return $user;
    }
}

/**
 * Interface IRegisterForm
 *
 * @package App\Forms
 */
interface IRegisterForm
{
    public function create(): RegisterForm;
}
