<?php
declare(strict_types=1);

namespace App\TaxDocumentModule\Forms;

use App\Entity\TaxDocument;
use App\Forms\AbstractForm;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;
use Nette\Localization\Translator;
use Nette\Security\User;

class TaxDocumentPaymentForm extends AbstractForm
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
        //
        $this->template->render(__DIR__.'/../templates/forms/tax-document-payment.latte');
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

        // Invoice
        $form->addHidden('id', 'ID');
        $form->addText('paidAt', 'Uhradené');
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
        $values = $form->getValues(true);
        /** @var TaxDocument|null $taxDocument */
        $taxDocument = $this->entityManager->getRepository(TaxDocument::class)->find((int)$values['id']);
        $ownerId     = $taxDocument && $taxDocument->getUserCompany() ? $taxDocument->getUserCompany()->getUser(
        )->getId() : null;

        if ( ! $taxDocument) {
            $form->addError('Nastala chyba pri nastavení úhrady dokladu');
        }

        if ($ownerId != $this->getLoggedUser()->getId()) {
            $form->addError('Nemáte právo upravovať daný doklad');
        }
    }

    public function onSuccess(Form $form): void
    {
        /** @var \Nette\Utils\ArrayHash $values */
        $values = $form->getValues(true);
        /** @var TaxDocument|null $taxDocument */
        $taxDocument = $this->entityManager->getRepository(TaxDocument::class)->find((int)$values['id']);

        // ------------------------------------- Tax document ---------------------------------------- \\

        $taxDocument->setPaidAt(new \DateTime($values['paidAt']));
        //
        $this->entityManager->flush();

        // Redirect to dashboard
        $this->presenter->flashMessage('Úhrada dokladu bola úspšene nastavená', 'success');
        //
        $this->presenter->redirect(':TaxDocument:List:default');
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
 * Interface ITaxDocumentPaymentForm
 *
 * @package App\Forms
 */
interface ITaxDocumentPaymentForm
{
    public function create(): TaxDocumentPaymentForm;
}