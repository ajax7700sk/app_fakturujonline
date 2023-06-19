<?php
declare(strict_types=1);

namespace App\TaxDocumentModule\Forms;

use App\Entity\Address;
use App\Entity\BankAccount;
use App\Entity\Contact;
use App\Entity\PaymentData;
use App\Entity\TaxDocument;
use App\Forms\AbstractForm;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;
use Nette\Localization\Translator;
use Nette\Security\User;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Currencies;

class TaxDocumentForm extends AbstractForm
{
    private EntityManagerInterface $entityManager;

    /** @var \Nette\Security\User */
    public $securityUser;
    private Translator $translator;
    /** @var TaxDocument|null */
    private $taxDocument;

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
        $this->template->render(__DIR__.'./../templates/forms/tax-document.latte');
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

        if ($this->taxDocument) {
            $form->addHidden('id', 'ID');
        }
        // Invoice
        $form->addSelect('type', 'Druh dokladu', [
            TaxDocument::TYPE_INVOICE => 'Faktúra',
            TaxDocument::TYPE_ADVANCE_PAYMENT => 'Zálohová faktúra',
            TaxDocument::TYPE_PROFORMA_INVOCE => 'Proforma faktúra',
            TaxDocument::TYPE_CREDIT_NOTE => 'Dobropis'
        ])
            ->setRequired("form.general.validation.required");
        $form->addText('number', 'Číslo dokladu')
             ->setRequired("form.general.validation.required");
        $form->addCheckbox('transferedTaxLiability', 'Preniesť daňovú zodpovednosť');
        $form->addCheckbox('vatPayer', 'Plátca DPH');
        $form->addText('issuedBy', 'Vystavil')
             ->setRequired("form.general.validation.required");
        $form->addText('issuedAt', 'Dátum vystavenia')
             ->setRequired("form.general.validation.required");
        $form->addText('deliveryDateAt', 'Dátum dodania')
             ->setRequired("form.general.validation.required");
        $form->addText('dueDateAt', 'Splatnosť')
             ->setRequired("form.general.validation.required");

        // Notes
        $form->addTextArea('noteAboutItems', 'Poznámka nad položkami');
        $form->addTextArea('note', 'Poznámka');

        // Settings
        $form->addSelect('currencyCode', 'Mena', Currencies::getCurrencyCodes())
            ->setRequired("form.general.validation.required");
        $form->addText('constantSymbol', 'Konštantný symbol');
        $form->addText('specificSymbol', 'Špecifický symbol');


        // Payment data
        $form->addSelect('paymentData_type', 'Typ', [
            PaymentData::TYPE_BANK_PAYMENT => 'Bankový prevod',
            PaymentData::TYPE_CASH_ON_DELIVERY => 'Dobierka',
            PaymentData::TYPE_CASH => 'Hotovosť',
            PaymentData::TYPE_PAYPAL => 'Paypal',
            PaymentData::TYPE_PAYMENT_CARD => 'Platobná karta'
        ])
             ->setRequired("form.general.validation.required");
        $form->addText('paymentData_bankAccount', 'Bankový účet');
        $form->addText('paymentData_iban', 'IBAN');
        $form->addText('paymentData_swift', 'SWIFT');


        $form->addCheckbox('billingSameAsShipping', 'Dodacia adresa je rovnaká')
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
        $form->addText('shippingAddress_vatNumber', 'IČ DPH');
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
        //
        $this->setDefaults($form);

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

        if ( ! $this->contact) {
            $contact = new Contact();
        } else {
            $contact = $this->contact;
        }

        //
        $contact->setName($values['name']);
        // TODO
        $contact->setBillingSameAsShipping($values['billingSameAsShipping']);

        // ------------------------------------- Bank account ---------------------------------------- \\

        if ( ! $contact->getBankAccount()) {
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

        if ( ! $this->contact && ! $contact->getBillingAddress()) {
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

        if ( ! $this->contact && ! $contact->getShippingAddress()) {
            $shippingAddress = new Address();
        } else {
            $shippingAddress = $contact->getShippingAddress();
        }

        // Fill address
        if ($contact->getBillingSameAsShipping()) {
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

        // Set relations
        $contact->setUser($this->getLoggedUser());
        $contact->setBillingAddress($billingAddress);
        $contact->setShippingAddress($shippingAddress);
        $contact->setBankAccount($bankAccount);

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

    public function setTaxDocument(TaxDocument $taxDocument): void
    {
        $this->taxDocument = $taxDocument;
    }

    private function setDefaults(Form $form): void
    {
        $defaults = array();

//        if ($this->taxDocument) {
//            $entity = $this->contact;
//            //
//            $defaults = array_merge($defaults, array(
//                // Company
//                'name'                  => $entity->getName(),
//                'billingSameAsShipping' => $entity->getBillingSameAsShipping(),
//            ));
//
//            // Billing address
//            if ($entity->getBillingAddress()) {
//                $billingAddress = $entity->getBillingAddress();
//
//                $defaults = array_merge($defaults, array(
//                    // Company
//                    'billingAddress_name'        => $billingAddress->getName(),
//                    'billingAddress_businessId'  => $billingAddress->getBusinessId(),
//                    'billingAddress_taxId'       => $billingAddress->getTaxId(),
//                    'billingAddress_vatNumber'   => $billingAddress->getVatNumber(),
//                    'billingAddress_phone'       => $billingAddress->getPhone(),
//                    'billingAddress_email'       => $billingAddress->getEmail(),
//                    'billingAddress_street'      => $billingAddress->getStreet(),
//                    'billingAddress_city'        => $billingAddress->getCity(),
//                    'billingAddress_zipCode'     => $billingAddress->getZipCode(),
//                    'billingAddress_countryCode' => $billingAddress->getCountryCode(),
//                ));
//            }
//
//            // Shipping address
//            if ($entity->getShippingAddress()) {
//                $shippingAddress = $entity->getShippingAddress();
//
//                $defaults = array_merge($defaults, array(
//                    // Company
//                    'shippingAddress_name'        => $shippingAddress->getName(),
//                    'shippingAddress_businessId'  => $shippingAddress->getBusinessId(),
//                    'shippingAddress_taxId'       => $shippingAddress->getTaxId(),
//                    'shippingAddress_vatNumber'   => $shippingAddress->getVatNumber(),
//                    'shippingAddress_phone'       => $shippingAddress->getPhone(),
//                    'shippingAddress_email'       => $shippingAddress->getEmail(),
//                    'shippingAddress_street'      => $shippingAddress->getStreet(),
//                    'shippingAddress_city'        => $shippingAddress->getCity(),
//                    'shippingAddress_zipCode'     => $shippingAddress->getZipCode(),
//                    'shippingAddress_countryCode' => $shippingAddress->getCountryCode(),
//                ));
//            }
//
//            // Bank account
//            if ($entity->getBankAccount()) {
//                $bankAccount = $entity->getBankAccount();
//
//                $defaults = array_merge($defaults, array(
//                    // Company
//                    'bankAccount_accountNumber' => $bankAccount->getAccountNumber(),
//                    'bankAccount_iban'          => $bankAccount->getIban(),
//                    'bankAccount_swift'         => $bankAccount->getSwift(),
//                ));
//            }
//        }

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
 * Interface ITaxDocumentForm
 *
 * @package App\Forms
 */
interface ITaxDocumentForm
{
    public function create(): TaxDocumentForm;
}