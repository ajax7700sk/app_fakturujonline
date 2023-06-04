<?php
declare(strict_types=1);

namespace App\SecurityModule\Forms;

use App\Forms\AbstractForm;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;

/**
 * Class RegisterForm
 *
 * @package App\Forms
 */
final class LoginForm extends AbstractForm
{
    private EntityManagerInterface $entityManager;

    /** @var \Nette\Security\User */
    public $securityUser;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * Render a form
     */
    public function render()
    {
        // Render
        $this->template->render(__DIR__.'./../templates/forms/login.latte');
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
        $form->setTranslator($this->translator);

        $form->addText('email', 'Váš e-mail')
             ->setRequired("form.general.validation.required")
             ->setAttribute("placeholder", 'Váš e-mail');
        $form->addText('password', 'Vaše heslo')
             ->setRequired("form.validation.required")
             ->setAttribute("placeholder", 'Vaše heslo');

        $form->addSubmit("submit", 'form.general.submit.label');

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
        $values = $form->getValues();

        try {
            $this->securityUser->login($values->email, $values->password);
            // Redirect to dashboard
            dd("Dashboard");
        } catch (\Nette\Security\AuthenticationException $e) {
            $this->presenter->flashMessage("form.login.validation.authentication");
            $this->presenter->redirect("this");
        }
    }
}

/**
 * Interface IRegisterForm
 *
 * @package App\Forms
 */
interface ILoginForm
{
    public function create(): LoginForm;
}
