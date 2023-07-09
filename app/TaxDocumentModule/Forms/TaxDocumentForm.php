<?php
declare(strict_types=1);

namespace App\TaxDocumentModule\Forms;

use App\Entity\Address;
use App\Entity\BankAccount;
use App\Entity\Contact;
use App\Entity\LineItem;
use App\Entity\PaymentData;
use App\Entity\TaxDocument;
use App\Entity\UserCompany;
use App\Forms\AbstractForm;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;
use Nette\Localization\Translator;
use Nette\Security\User;
use App\Intl\Countries;
use App\Intl\Currencies;

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
        $this->template->taxDocument = $this->taxDocument;
        //
        $this->template->render(__DIR__.'/../templates/forms/tax-document.latte');
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

        if ($this->taxDocument) {
            $form->addHidden('id', 'ID');
        }

        // Companies
        $companiesList = ['--- Vybrať ---'];

        foreach ($this->getLoggedUser()->getUserCompanies() as $userCompany) {
            $companiesList[$userCompany->getId()] = $userCompany->getName();
        }

        // Invoice
        $form->addSelect('userCompany', 'Spoločnosť', $companiesList)
            ->setRequired("Pole je povinné");
        $form->addSelect('type', 'Druh dokladu', [
            TaxDocument::TYPE_INVOICE => 'Faktúra',
            TaxDocument::TYPE_ADVANCE_PAYMENT => 'Zálohová faktúra',
            TaxDocument::TYPE_PROFORMA_INVOCE => 'Proforma faktúra',
            TaxDocument::TYPE_CREDIT_NOTE => 'Dobropis'
        ])
            ->setRequired("Pole je povinné");
        $form->addText('number', 'Číslo dokladu')
             ->setRequired("Pole je povinné");
        $form->addCheckbox('transferedTaxLiability', 'Preniesť daňovú zodpovednosť');
        $form->addCheckbox('vatPayer', 'Plátca DPH');
        $form->addText('issuedBy', 'Vystavil')
             ->setRequired("Pole je povinné");
        $form->addText('issuedAt', 'Dátum vystavenia')
             ->setRequired("Pole je povinné");
        $form->addText('deliveryDateAt', 'Dátum dodania')
             ->setRequired("Pole je povinné");
        $form->addText('dueDateAt', 'Splatnosť')
             ->setRequired("Pole je povinné");

        // Notes
        $form->addTextArea('noteAboveItems', 'Poznámka nad položkami');
        $form->addTextArea('note', 'Poznámka');

        // Settings
        $form->addSelect('currencyCode', 'Mena', Currencies::getNames())
            ->setRequired("Pole je povinné");
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
             ->setRequired("Pole je povinné");
        $form->addText('paymentData_bankAccount', 'Bankový účet');
        $form->addText('paymentData_iban', 'IBAN');
        $form->addText('paymentData_swift', 'SWIFT');

        // Supplier
        $form->addText('supplier_name', 'Názov spoločnosti')
             ->setRequired("Pole je povinné");
        $form->addText('supplier_businessId', 'IČO')
             ->setRequired("Pole je povinné");
        $form->addText('supplier_taxId', 'DIČ');
        $form->addText('supplier_vatNumber', 'IČ DPH');
        $form->addText('supplier_phone', 'Telefon');
        $form->addText('supplier_email', 'E-mail')
             ->setRequired("Pole je povinné");
        $form->addText('supplier_street', 'Adresa')
             ->setRequired("Pole je povinné");
        $form->addText('supplier_city', 'Město')
             ->setRequired("Pole je povinné");
        $form->addText('supplier_zipCode', 'PŠC')
             ->setRequired("Pole je povinné");
        $form->addSelect('supplier_countryCode', 'Štát', Countries::getNames())
             ->setRequired("Pole je povinné");

        // Subscriber address
        $form->addText('subscriber_name', 'Názov spoločnosti')
             ->setRequired("Pole je povinné");
        $form->addText('subscriber_businessId', 'IČO')
             ->setRequired("Pole je povinné");
        $form->addText('subscriber_taxId', 'DIČ');
        $form->addText('subscriber_vatNumber', 'IČ DPH');
        $form->addText('subscriber_phone', 'Telefon');
        $form->addText('subscriber_email', 'E-mail')
             ->setRequired("Pole je povinné");
        $form->addText('subscriber_street', 'Adresa')
             ->setRequired("Pole je povinné");
        $form->addText('subscriber_city', 'Město')
             ->setRequired("Pole je povinné");
        $form->addText('subscriber_zipCode', 'PŠC')
             ->setRequired("Pole je povinné");
        $form->addSelect('subscriber_countryCode', 'Štát', Countries::getNames())
             ->setRequired("Pole je povinné");

        // Supplier bank account
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


        // ------------------------------------- Tax document ---------------------------------------- \\

        if ( ! $this->taxDocument) {
            $taxDocument = new TaxDocument();
        } else {
            $taxDocument = $this->taxDocument;
        }

        // User company
        /** @var UserCompany|null $userCompany */
        $userCompany = $this->entityManager
            ->getRepository(UserCompany::class)
            ->find((int)$values['userCompany']);

        //
        $taxDocument->setUserCompany($userCompany);
        //
        $taxDocument->setType($values['type']);
        $taxDocument->setNumber($values['number']);
        $taxDocument->setTransferedTaxLiability($values['transferedTaxLiability']);
        $taxDocument->setVatPayer($values['vatPayer']);
        $taxDocument->setIssuedBy($values['issuedBy']);
        $taxDocument->setIssuedAt(new \DateTime($values['issuedAt']));
        $taxDocument->setDeliveryDateAt(new \DateTime($values['deliveryDateAt']));
        $taxDocument->setDueDateAt(new \DateTime($values['dueDateAt']));
        $taxDocument->setLocaleCode('SK');
        //
        $taxDocument->setNoteAboveItems($values['noteAboveItems']);
        $taxDocument->setNote($values['note']);
        //
        $taxDocument->setCurrencyCode($values['currencyCode']);
        $taxDocument->setConstantSymbol($values['constantSymbol']);
        $taxDocument->setSpecificSymbol($values['specificSymbol']);

        // ------------------------------------- Payment data ---------------------------------------- \\

        if ( ! $taxDocument->getPaymentData()) {
            $paymentData = new PaymentData();
        } else {
            $paymentData = $taxDocument->getPaymentData();
        }

        $paymentData->setType($values['paymentData_type']);
        $paymentData->setBankAccountNumber($values['paymentData_bankAccount']);
        $paymentData->setBankAccountIban($values['paymentData_iban']);
        $paymentData->setBankAccountSwift($values['paymentData_swift']);

        // ------------------------------------- Supplier ---------------------------------------- \\

        if ( ! $taxDocument->getSupplierBillingAddress()) {
            $supplier = new Address();
        } else {
            $supplier = $taxDocument->getSupplierBillingAddress();
        }

        $supplier->setName($values['supplier_name']);
        $supplier->setBusinessId($values['supplier_businessId']);
        $supplier->setTaxId($values['supplier_taxId']);
        $supplier->setVatNumber($values['supplier_vatNumber']);
        $supplier->setPhone($values['supplier_phone']);
        $supplier->setEmail($values['supplier_email']);
        $supplier->setStreet($values['supplier_street']);
        $supplier->setCity($values['supplier_city']);
        $supplier->setZipCode($values['supplier_zipCode']);
        $supplier->setCountryCode($values['supplier_countryCode']);

        // ------------------------------------- Shipping address ---------------------------------------- \\

        // Fill address
        if (!$taxDocument->getSubscriberBillingAddress()) {
            $subscriber = new Address();
        } else {
            $subscriber = $taxDocument->getSubscriberBillingAddress();
        }

        $subscriber->setName($values['subscriber_name']);
        $subscriber->setBusinessId($values['subscriber_businessId']);
        $subscriber->setTaxId($values['subscriber_taxId']);
        $subscriber->setVatNumber($values['subscriber_vatNumber']);
        $subscriber->setPhone($values['subscriber_phone']);
        $subscriber->setEmail($values['subscriber_email']);
        $subscriber->setStreet($values['subscriber_street']);
        $subscriber->setCity($values['subscriber_city']);
        $subscriber->setZipCode($values['subscriber_zipCode']);
        $subscriber->setCountryCode($values['subscriber_countryCode']);

        // ------------------------------------- Bank account ---------------------------------------- \\

        if(!$taxDocument->getBankAccount()) {
            $bankAccount = new BankAccount();
        } else {
            $bankAccount = $taxDocument->getBankAccount();
        }

        $bankAccount->setAccountNumber($values['bankAccount_accountNumber']);
        $bankAccount->setIban($values['bankAccount_iban']);
        $bankAccount->setSwift($values['bankAccount_swift']);

        // ------------------------------------- Items ---------------------------------------- \\

        $httpData = $form->getHttpData();

        // Remove items
        $taxDocument->clearLineItems();

        // Items
        foreach ($httpData['lineItems'] as $_lineItem) {
            $lineItem = new LineItem();
            $lineItem->setName($_lineItem['name']);
            $lineItem->setQuantity((int) $_lineItem['quantity']);
            $lineItem->setUnit($_lineItem['unit']);
            $lineItem->setType('line_item');
            // Price + Tax rate
            $lineItem->setTaxRate($_lineItem['taxRate']);
            $lineItem->setUnitPriceTaxExcl($_lineItem['unitPriceTaxExcl']);
            //
            $taxDocument->addLineItem($lineItem);
        }

        // Set relations
        $taxDocument->setSupplierBillingAddress($supplier);
        $taxDocument->setSubscriberBillingAddress($subscriber);
        $taxDocument->setPaymentData($paymentData);
        $taxDocument->setBankAccount($bankAccount);

        // Persist & flush
        $this->entityManager->persist($paymentData);
        $this->entityManager->persist($supplier);
        $this->entityManager->persist($subscriber);
        $this->entityManager->persist($bankAccount);
        $this->entityManager->persist($taxDocument);
        //
        $this->entityManager->flush();

        // Redirect to dashboard
        if($this->taxDocument) {
            $this->presenter->flashMessage('Doklad bol úspešne aktualizovaný', 'success');
        } else {
            $this->presenter->flashMessage('Doklad bol úspešne vytvorený', 'success');
        }
        //
        $this->presenter->redirect(':TaxDocument:List:default');
    }

    // ------------------------------------ Helpers ---------------------------------- \\

    public function setTaxDocument(?TaxDocument $taxDocument): void
    {
        $this->taxDocument = $taxDocument;
    }

    private function setDefaults(Form $form): void
    {
        $defaults = array();

        if ($this->taxDocument) {
            $entity = $this->taxDocument;
            //
            $defaults = array_merge($defaults, array(
                'type'           => $entity->getType(),
                'number'         => $entity->getNumber(),
                'vatPayer'       => $entity->getVatPayer(),
                'issuedBy'       => $entity->getIssuedBy(),
                'issuedAt'       => $entity->getIssuedAt()->format('Y-m-d'),
                'deliveryDateAt' => $entity->getDeliveryDateAt()->format('Y-m-d'),
                'dueDateAt'      => $entity->getDueDateAt()->format('Y-m-d'),
                'noteAboveItems' => $entity->getNoteAboveItems(),
                'note'           => $entity->getNote(),
                'currencyCode'   => $entity->getCurrencyCode(),
                'constantSymbol' => $entity->getConstantSymbol(),
                'specificSymbol' => $entity->getSpecificSymbol(),
                'userCompany'    => $entity->getUserCompany()->getId(),
            ));

            // Payment data
            if($entity->getPaymentData()) {
                $paymentData = $entity->getPaymentData();
                //
                $defaults = array_merge($defaults, array(
                    'paymentData_type' => $paymentData->getType(),
                    'paymentData_bankAccount' => $paymentData->getBankAccountNumber(),
                    'paymentData_iban' => $paymentData->getBankAccountIban(),
                    'paymentData_swift' => $paymentData->getBankAccountSwift()
                ));
            }

            // Supplier
            if ($entity->getSupplierBillingAddress()) {
                $billingAddress = $entity->getSupplierBillingAddress();

                $defaults = array_merge($defaults, array(
                    // Company
                    'supplier_name'        => $billingAddress->getName(),
                    'supplier_businessId'  => $billingAddress->getBusinessId(),
                    'supplier_taxId'       => $billingAddress->getTaxId(),
                    'supplier_vatNumber'   => $billingAddress->getVatNumber(),
                    'supplier_phone'       => $billingAddress->getPhone(),
                    'supplier_email'       => $billingAddress->getEmail(),
                    'supplier_street'      => $billingAddress->getStreet(),
                    'supplier_city'        => $billingAddress->getCity(),
                    'supplier_zipCode'     => $billingAddress->getZipCode(),
                    'supplier_countryCode' => $billingAddress->getCountryCode(),
                ));
            }

            // Subscriber
            if ($entity->getSubscriberBillingAddress()) {
                $billingAddress = $entity->getSubscriberBillingAddress();

                $defaults = array_merge($defaults, array(
                    // Company
                    'subscriber_name'        => $billingAddress->getName(),
                    'subscriber_businessId'  => $billingAddress->getBusinessId(),
                    'subscriber_taxId'       => $billingAddress->getTaxId(),
                    'subscriber_vatNumber'   => $billingAddress->getVatNumber(),
                    'subscriber_phone'       => $billingAddress->getPhone(),
                    'subscriber_email'       => $billingAddress->getEmail(),
                    'subscriber_street'      => $billingAddress->getStreet(),
                    'subscriber_city'        => $billingAddress->getCity(),
                    'subscriber_zipCode'     => $billingAddress->getZipCode(),
                    'subscriber_countryCode' => $billingAddress->getCountryCode(),
                ));
            }

            // Bank account
            if ($entity->getBankAccount()) {
                $bankAccount = $entity->getBankAccount();

                $defaults = array_merge($defaults, array(
                    // Company
                    'bankAccount_accountNumber' => $bankAccount->getAccountNumber(),
                    'bankAccount_iban'          => $bankAccount->getIban(),
                    'bankAccount_swift'         => $bankAccount->getSwift(),
                ));
            }

        }

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


    /*********************************************************************
     * Handlers
     ********************************************************************/

    public function handleLoadCompanyData(): void
    {
        $id = $this->presenter->request->getPost('id');
        //
        /** @var UserCompany|null $company */
        $company = $this->entityManager
            ->getRepository(UserCompany::class)
            ->find((int) $id);

        if(!$company) {
            $this->error();
        }


        //
        $billingAddress = $company->getBillingAddress();
        $bankAccount = $company->getBankAccount();

        // Send data
        $this->presenter->sendJson(array(
            'supplier_name' => $company->getName(),
            'supplier_businessId' => $billingAddress ? $billingAddress->getBusinessId() : null,
            'supplier_taxId' => $billingAddress ? $billingAddress->getTaxId() : null,
            'supplier_vatNumber' => $billingAddress ? $billingAddress->getVatNumber() : null,
            'supplier_phone' => $billingAddress ? $billingAddress->getPhone() : null,
            'supplier_email' => $billingAddress ? $billingAddress->getEmail() : null,
            'supplier_street' => $billingAddress ? $billingAddress->getStreet() : null,
            'supplier_city' => $billingAddress ? $billingAddress->getCity() : null,
            'supplier_zipCode' => $billingAddress ? $billingAddress->getZipCode() : null,
            'supplier_countryCode' => $billingAddress ? $billingAddress->getCountryCode() : null,
            // Bank
            'paymentData_bankAccount' => $bankAccount ? $bankAccount->getAccountNumber() : null,
            'paymentData_iban' => $bankAccount ? $bankAccount->getIban() : null,
            'paymentData_swift' => $bankAccount ? $bankAccount->getSwift() : null
        ));
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