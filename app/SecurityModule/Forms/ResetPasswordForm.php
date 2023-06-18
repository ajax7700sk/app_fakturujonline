<?php
declare(strict_types=1);

namespace App\SecurityModule\Forms;

use App\Entity\User;
use App\Forms\AbstractForm;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;
use Nette\Localization\Translator;
use Nette\Security\User as SecurityUser;

final class ResetPasswordForm extends AbstractForm
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
        $this->template->render(__DIR__.'./../templates/forms/reset.latte');
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

        $form->addEmail('email', 'E-mail')
             ->setRequired("form.general.validation.required");

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

        if ( ! $user) {
            $form->addError('Užívateľ s týmto e-mailom neexistuje');
        }
    }

    public function onSuccess(Form $form): void
    {
        /** @var \Nette\Utils\ArrayHash $values */
        $values = $form->getValues();

        try {
            // Redirect to dashboard
            $this->presenter->flashMessage('Na váš e-mail bol odoslaný odkaz k obnoveniu hesla', 'success');
            $this->presenter->redirect(':Security:Auth:login');
        } catch (\Exception $e) {
            // TODO: odchytit transport email exception
            dd($e->getMessage());
            $this->presenter->flashMessage("Pri odoslaní e-mailu nastala neočakávana chyba");
            $this->presenter->redirect("this");
        }
    }

}

/**
 * Interface IRegisterForm
 *
 * @package App\Forms
 */
interface IResetPasswordForm
{
    public function create(): ResetPasswordForm;
}