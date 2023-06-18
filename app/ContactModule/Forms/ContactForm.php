<?php
declare(strict_types=1);

namespace App\ContactModule\Forms;

use App\Forms\AbstractForm;
use App\SecurityModule\Forms\LoginForm;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;
use Nette\Localization\Translator;
use Nette\Security\User;
use Symfony\Component\Intl\Countries;

class ContactForm extends AbstractForm
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
        $this->template->render(__DIR__.'./../templates/forms/contact.latte');
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

        $form->addText('name', 'Názov kontaktu')
             ->setRequired("form.general.validation.required")
             ->setAttribute("placeholder", 'Váš e-mail');
        $form->addText('billingSameAsShipping', 'Dodacia adresa je rovnaká')
             ->setAttribute("placeholder", 'Dodacia adresa je rovnaká');

        // Billing address
        $form->addText('billingAddress_name', 'Názov spoločnosti')
            ->setRequired("form.general.validation.required");
        $form->addText('billingAddress_businessId', 'IČO')
             ->setRequired("form.general.validation.required");
        $form->addText('billingAddress_taxId', 'DIČ')
             ->setRequired("form.general.validation.required");
        $form->addText('billingAddress_vatNumber', 'IČ DPH')
             ->setRequired("form.general.validation.required");
        $form->addText('billingAddress_phone', 'Telefon')
             ->setRequired("form.general.validation.required");
        $form->addText('billingAddress_email', 'E-mail')
             ->setRequired("form.general.validation.required");
        $form->addText('billingAddress_street', 'Adresa')
             ->setRequired("form.general.validation.required");
        $form->addText('billingAddress_city', 'Město')
             ->setRequired("form.general.validation.required");
        $form->addText('billingAddress_zipCode', 'PŠC')
             ->setRequired("form.general.validation.required");
        $form->addSelect('billingAddress_countryCode', 'Štát', Countries::getNames())
            ->setRequired("form.general.validation.required");

        // Shipping address
        $form->addText('shippingAddress_name', 'Názov spoločnosti')
             ->setRequired("form.general.validation.required");
        $form->addText('shippingAddress_businessId', 'IČO')
             ->setRequired("form.general.validation.required");
        $form->addText('shippingAddress_taxId', 'DIČ')
             ->setRequired("form.general.validation.required");
        $form->addText('shippingAddress_vatNumber', 'IČ DPH')
             ->setRequired("form.general.validation.required");
        $form->addText('shippingAddress_phone', 'Telefon')
             ->setRequired("form.general.validation.required");
        $form->addText('shippingAddress_email', 'E-mail')
             ->setRequired("form.general.validation.required");
        $form->addText('shippingAddress_street', 'Adresa')
             ->setRequired("form.general.validation.required");
        $form->addText('shippingAddress_city', 'Město')
             ->setRequired("form.general.validation.required");
        $form->addText('shippingAddress_zipCode', 'PŠC')
             ->setRequired("form.general.validation.required");
        $form->addSelect('shippingAddress_countryCode', 'Štát', Countries::getNames())
             ->setRequired("form.general.validation.required");

        // Bank account
        $form->addText('bankAccount_accountNumber', 'Číslo účtu');
        $form->addText('bankAccount_iban', 'IBAN');
        $form->addText('bankAccount_swift', 'SWIFT');

        //
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

        // TODO: process
        dd("Test");
        // Redirect to dashboard
        $this->presenter->flashMessage('Kontakt bol úspešne vytvorený', 'success');
        $this->presenter->redirect(':Contact:List:default');
    }

    // ------------------------------------ Helpers ---------------------------------- \\

    private function addAddressFields($prefix, Form $form): Form
    {
        $form->addText(sprintf('%s[name]', $prefix), 'Názov kontaktu')
             ->setRequired("form.general.validation.required")
             ->setAttribute("placeholder", 'Váš e-mail');
    }
}

/**
 * Interface IContactForm
 *
 * @package App\Forms
 */
interface IContactForm
{
    public function create(): ContactForm;
}