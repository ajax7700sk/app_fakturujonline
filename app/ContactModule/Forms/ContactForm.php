<?php
declare(strict_types=1);

namespace App\ContactModule\Forms;

use App\Entity\Address;
use App\Entity\BankAccount;
use App\Entity\Contact;
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
    /** @var Contact */
    private $contact;

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

        if($this->contact) {
            $form->addHidden('id', 'ID');
        }
        //
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
        $form->addText('billingAddress_vatNumber', 'IČ DPH');
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
        $values = $form->getValues(true);


        // ------------------------------------- Contact ---------------------------------------- \\

        if(!$this->contact) {
            $contact = new Contact();
        } else {
            $contact = $this->contact;
        }

        //
        $contact->setName($values['name']);
        // TODO
        $contact->setBillingSameAsShipping($values['billingSameAsShipping']);

        // ------------------------------------- Bank account ---------------------------------------- \\

        if(!$this->contact && !$contact->getBankAccount()) {
            $bankAccount = new BankAccount();
        } else {
            $bankAccount = $contact->getBankAccount();
        }

        $bankAccount->setAccountNumber($values['bankAccount_accountNumber']);
        $bankAccount->setIban($values['bankAccount_iban']);
        $bankAccount->setSwift($values['bankAccount_swift']);
        //
        $contact->setBankAccount($bankAccount);

        // ------------------------------------- Billing address ---------------------------------------- \\

        if(!$this->contact && !$contact->getBillingAddress()) {
            $billingAddress = new Address();
        } else {
            $billingAddress = $contact->getBillingAddress();
        }

        $billingAddress->setName($values['billingAddress_name']);
        $billingAddress->setBusinessId($values['billingAddress_businessId']);
        $billingAddress->setTaxId($values['billingAddress_taxId']);
        $billingAddress->setVatNumber($values['billingAddress_vatNumber']);
        $billingAddress->setPhone($values['billingAddress_phone']);
        $billingAddress->setEmail($values['billingAddress_email']);
        $billingAddress->setStreet($values['billingAddress_street']);
        $billingAddress->setCity($values['billingAddress_city']);
        $billingAddress->setZipCode($values['billingAddress_zipCode']);
        $billingAddress->setCountryCode($values['billingAddress_countryCode']);


        // ------------------------------------- Shipping address ---------------------------------------- \\

        if(!$this->contact && !$contact->getShippingAddress()) {
            $shippingAddress = new Address();
        } else {
            $shippingAddress = $contact->getShippingAddress();
        }

        // Fill address
        if($contact->getBillingSameAsShipping()) {
            $shippingAddress = $billingAddress;
        } else {
            $shippingAddress->setName($values['billingAddress_name']);
            $shippingAddress->setBusinessId($values['billingAddress_businessId']);
            $shippingAddress->setTaxId($values['billingAddress_taxId']);
            $shippingAddress->setVatNumber($values['billingAddress_vatNumber']);
            $shippingAddress->setPhone($values['billingAddress_phone']);
            $shippingAddress->setEmail($values['billingAddress_email']);
            $shippingAddress->setStreet($values['billingAddress_street']);
            $shippingAddress->setCity($values['billingAddress_city']);
            $shippingAddress->setZipCode($values['billingAddress_zipCode']);
            $shippingAddress->setCountryCode($values['billingAddress_countryCode']);
        }

        // Persist & flush
        $this->entityManager->persist($billingAddress);
        $this->entityManager->persist($shippingAddress);
        $this->entityManager->persist($bankAccount);
        $this->entityManager->persist($contact);
        //
        $this->entityManager->flush();

        // Redirect to dashboard
        $this->presenter->flashMessage('Kontakt bol úspešne vytvorený', 'success');
        $this->presenter->redirect(':Contact:List:default');
    }

    // ------------------------------------ Helpers ---------------------------------- \\

    public function setContact(Contact $contact): void
    {
        $this->contact = $contact;
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