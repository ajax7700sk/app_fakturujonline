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
    /** @var UserCompany|null */
    private $userCompany;

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
        $this->template->lastTaxDocument = $this->getLastTaxDocument();
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
        $contacts = ['--- Vybrať ---'];
        $user = $this->getLoggedUser();

        if($user) {
            foreach ($user->getContacts() as $contact) {
                $contacts[$contact->getId()] = $contact->getName();
            }
        }

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
             ->setRequired("Pole 'Spoločnosť' je povinné");
        if ($this->userCompany) {
            $form['userCompany']->setDisabled(true);
        }
        $form->addSelect('type', 'Druh dokladu', [
            TaxDocument::TYPE_INVOICE         => 'Faktúra',
            TaxDocument::TYPE_ADVANCE_PAYMENT => 'Zálohová faktúra',
            TaxDocument::TYPE_PROFORMA_INVOCE => 'Proforma faktúra',
            TaxDocument::TYPE_CREDIT_NOTE     => 'Dobropis',
        ])
             ->setRequired("Pole 'Druh dokladu' je povinné");
        $form->addText('number', 'Číslo dokladu')
             ->setRequired("Pole 'Číslo dokladu' je povinné");
        $form->addText('evidenceNumber', 'Evidenčné číslo dokladu');
        $form->addCheckbox('transferedTaxLiability', 'Preniesť daňovú zodpovednosť');
        $form->addCheckbox('vatPayer', 'Plátca DPH');
        $form->addText('issuedBy', 'Vystavil')
             ->setRequired("Pole 'Vystavil' je povinné");
        $form->addText('issuedAt', 'Dátum vystavenia')
             ->setRequired("Pole 'Dátum vystavenia' je povinné");
        $form->addText('deliveryDateAt', 'Dátum dodania')
             ->setRequired("Pole 'Dátum dodania' je povinné");
        $form->addText('dueDateAt', 'Splatnosť')
             ->setRequired("Pole 'Splatnosť' je povinné");

        // Notes
        $form->addTextArea('noteAboveItems', 'Poznámka nad položkami');
        $form->addTextArea('note', 'Poznámka');

        // Settings
        $form->addSelect('currencyCode', 'Mena', Currencies::getNames())
             ->setRequired("Pole 'Mena' je povinné");
        $form->addText('constantSymbol', 'Konštantný symbol');
        $form->addText('specificSymbol', 'Špecifický symbol');

        // Payment data
        $form->addSelect('paymentData_type', 'Typ', [
            PaymentData::TYPE_BANK_PAYMENT     => 'Bankový prevod',
            PaymentData::TYPE_CASH_ON_DELIVERY => 'Dobierka',
            PaymentData::TYPE_CASH             => 'Hotovosť',
            PaymentData::TYPE_PAYPAL           => 'Paypal',
            PaymentData::TYPE_PAYMENT_CARD     => 'Platobná karta',
        ])
             ->setRequired("Pole 'Typ' je povinné");
        $form->addText('paymentData_paypalMail', 'PayPal mail');
        $form->addText('paymentData_bankAccount', 'Bankový účet');
        $form->addText('paymentData_iban', 'IBAN');
        $form->addText('paymentData_swift', 'SWIFT');

        // Supplier
        $form->addText('supplier_name', 'Názov spoločnosti')
             ->setRequired("Pole 'Názov spoločnosti' je povinné");
        $form->addText('supplier_businessId', 'IČO');
        $form->addText('supplier_taxId', 'DIČ');
        $form->addText('supplier_vatNumber', 'IČ DPH');
        $form->addText('supplier_phone', 'Telefon');
        $form->addText('supplier_email', 'E-mail');
        $form->addText('supplier_street', 'Adresa');
        $form->addText('supplier_city', 'Město');
        $form->addText('supplier_zipCode', 'PŠC');
        $form->addSelect('supplier_countryCode', 'Štát', Countries::getNames())
             ->setRequired("Pole 'Štát' je povinné");

        // Subscriber address
        $form->addSelect('contact', 'Odberateľ', $contacts);
        $form->addText('subscriber_name', 'Názov spoločnosti')
             ->setRequired("Pole 'Názov spoločnosti' je povinné");
        $form->addText('subscriber_businessId', 'IČO');
        $form->addText('subscriber_taxId', 'DIČ');
        $form->addText('subscriber_vatNumber', 'IČ DPH');
        $form->addText('subscriber_phone', 'Telefon');
        $form->addText('subscriber_email', 'E-mail');
        $form->addText('subscriber_street', 'Adresa');
        $form->addText('subscriber_city', 'Město');
        $form->addText('subscriber_zipCode', 'PŠC');
        $form->addSelect('subscriber_countryCode', 'Štát', Countries::getNames())
             ->setRequired("Pole 'Štát' je povinné");

        // Supplier bank account
        $form->addText('bankAccount_accountNumber', 'Číslo účtu');
        $form->addText('bankAccount_iban', 'IBAN');
        $form->addText('bankAccount_swift', 'SWIFT');

        //
        $form->addSubmit("submit", 'form.general.submit.label');
        $form
            ->addSubmit("submitDraft", 'form.general.draft.label')
            ->setValidationScope([]);
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

        // --- Validate number
        //
        $currentNumber = $this->taxDocument ? $this->taxDocument->getNumber() : null;
        $newNumber = $values['number'];

        // Check if there is at least one tax document with same number
        if($currentNumber != $newNumber) {
            $exists = $this->checkIfNumberExists($newNumber);

            if($exists) {
                $form->addError('Číslo dokladu je už použité pri inom doklade');

                return;
            }
        }

        // --- Validate evidence number
        //
        $currentNumber = $this->taxDocument ? $this->taxDocument->getEvidenceNumber() : null;
        $newNumber = $values['evidenceNumber'];

        // Check if there is at least one tax document with same number
        if($currentNumber != $newNumber) {
            $exists = $this->checkIfEvidenceNumberExists($newNumber);

            if($exists) {
                $form->addError('Evidenčné číslo dokladu je už použité pri inom doklade');

                return;
            }
        }
    }

    public function onSuccess(Form $form): void
    {
        /** @var \Nette\Utils\ArrayHash $values */
        $values = $form->getValues(true);
        $httpData = $form->getHttpData();
        $isDraft = isset($httpData['submitDraft']) ? true : false;
        $values = $isDraft ? $httpData : $values;
        $user = $this->getLoggedUser();
        /** @var Contact|null $contact */
        $contact = null;

        // ------------------------------------- Tax document ---------------------------------------- \\

        if ( ! $this->taxDocument) {
            $taxDocument = new TaxDocument();
        } else {
            $taxDocument = $this->taxDocument;
        }

        $taxDocument->setPublishState($isDraft ? 'draft' : 'publish');
        $userCompany = null;

        // User company
        /** @var UserCompany|null $userCompany */
        if ( ! $this->userCompany) {
            if($values['userCompany']) {
                $userCompany = $this->entityManager
                    ->getRepository(UserCompany::class)
                    ->find((int)$values['userCompany']);
            }
        } else {
            $userCompany = $this->userCompany;
        }

        // Contact
        if($user && isset($values['contact'])) {
            foreach ($user->getContacts() as $_contact) {
                if($_contact->getId() == $values['contact']) {
                    $taxDocument->setContact($_contact);
                }
            }
        }

        //
        $taxDocument->setUserCompany($userCompany);
        //
        $taxDocument->setType(isset($values['type']) ? $values['type'] : TaxDocument::TYPE_INVOICE);
        $taxDocument->setNumber(isset($values['number']) ? $values['number'] : "");
        $taxDocument->setEvidenceNumber(isset($values['evidenceNumber']) ? $values['evidenceNumber'] : "");
        $taxDocument->setTransferedTaxLiability(isset($values['transferedTaxLiability']) ? $this->checkboxValue($values['transferedTaxLiability']) : false);
        $taxDocument->setVatPayer(isset($values['vatPayer']) ? $this->checkboxValue($values['vatPayer']) : false);
        $taxDocument->setIssuedBy(isset($values['issuedBy']) ? $values['issuedBy'] : null);
        $taxDocument->setIssuedAt(isset($values['issuedAt']) ? new \DateTime($values['issuedAt']) : null);
        $taxDocument->setDeliveryDateAt(isset($values['deliveryDateAt']) ? new \DateTime($values['deliveryDateAt']) : null);
        $taxDocument->setDueDateAt(isset($values['dueDateAt']) ? new \DateTime($values['dueDateAt']) : null);
        $taxDocument->setLocaleCode('SK');
        //
        $taxDocument->setNoteAboveItems(isset($values['noteAboveItems']) ? $values['noteAboveItems'] : null);
        $taxDocument->setNote(isset($values['note']) ? $values['note'] : null);
        //
        $taxDocument->setCurrencyCode(isset($values['currencyCode'])  ? $values['currencyCode'] : null);
        $taxDocument->setConstantSymbol(isset($values['constantSymbol']) ? $values['constantSymbol'] : null);
        $taxDocument->setSpecificSymbol(isset($values['specificSymbol']) ? $values['specificSymbol'] : null);

        // ------------------------------------- Payment data ---------------------------------------- \\

        if ( ! $taxDocument->getPaymentData()) {
            $paymentData = new PaymentData();
        } else {
            $paymentData = $taxDocument->getPaymentData();
        }

        $paymentData->setType(isset($values['paymentData_type']) ? $values['paymentData_type'] : null);
        $paymentData->setPaypalMail(isset($values['paymentData_paypalMail']) ? $values['paymentData_paypalMail'] : null);
        $paymentData->setBankAccountNumber(isset($values['paymentData_bankAccount']) ? $values['paymentData_bankAccount'] : null);
        $paymentData->setBankAccountIban(isset($values['paymentData_iban']) ? $values['paymentData_iban'] : null);
        $paymentData->setBankAccountSwift(isset($values['paymentData_swift']) ? $values['paymentData_swift'] : null);

        // ------------------------------------- Supplier ---------------------------------------- \\

        if ( ! $taxDocument->getSupplierBillingAddress()) {
            $supplier = new Address();
        } else {
            $supplier = $taxDocument->getSupplierBillingAddress();
        }

        $supplier->setName(isset($values['supplier_name']) ? $values['supplier_name'] : "");
        $supplier->setBusinessId(isset($values['supplier_businessId']) ? $values['supplier_businessId'] : null);
        $supplier->setTaxId(isset($values['supplier_taxId']) ? $values['supplier_taxId'] : null);
        $supplier->setVatNumber(isset($values['supplier_vatNumber']) ? $values['supplier_vatNumber'] : null);
        $supplier->setPhone(isset($values['supplier_phone']) ? $values['supplier_phone'] : null);
        $supplier->setEmail(isset($values['supplier_email']) ? $values['supplier_email'] : null);
        $supplier->setStreet(isset($values['supplier_street']) ? $values['supplier_street'] : "");
        $supplier->setCity(isset($values['supplier_city']) ? $values['supplier_city'] : "");
        $supplier->setZipCode(isset($values['supplier_zipCode']) ? $values['supplier_zipCode'] : "");
        $supplier->setCountryCode(isset($values['supplier_countryCode']) ? $values['supplier_countryCode'] : "");

        // ------------------------------------- Shipping address ---------------------------------------- \\

        // Fill address
        if ( ! $taxDocument->getSubscriberBillingAddress()) {
            $subscriber = new Address();
        } else {
            $subscriber = $taxDocument->getSubscriberBillingAddress();
        }

        $subscriber->setName(isset($values['subscriber_name']) ? $values['subscriber_name'] : "");
        $subscriber->setBusinessId(isset($values['subscriber_businessId']) ? $values['subscriber_businessId'] : null);
        $subscriber->setTaxId(isset($values['subscriber_taxId']) ? $values['subscriber_taxId'] : null);
        $subscriber->setVatNumber(isset($values['subscriber_vatNumber']) ? $values['subscriber_vatNumber'] : null);
        $subscriber->setPhone(isset($values['subscriber_phone']) ? $values['subscriber_phone'] : null);
        $subscriber->setEmail(isset($values['subscriber_email']) ? $values['subscriber_email'] : null);
        $subscriber->setStreet(isset($values['subscriber_street']) ? $values['subscriber_street'] : "");
        $subscriber->setCity(isset($values['subscriber_city']) ? $values['subscriber_city'] : "");
        $subscriber->setZipCode(isset($values['subscriber_zipCode']) ? $values['subscriber_zipCode'] : "");
        $subscriber->setCountryCode(isset($values['subscriber_countryCode']) ? $values['subscriber_countryCode'] : "");

        // ------------------------------------- Bank account ---------------------------------------- \\

        if ( ! $taxDocument->getBankAccount()) {
            $bankAccount = new BankAccount();
        } else {
            $bankAccount = $taxDocument->getBankAccount();
        }

        $bankAccount->setAccountNumber(isset($values['bankAccount_accountNumber']) ? $values['bankAccount_accountNumber'] : null);
        $bankAccount->setIban(isset($values['bankAccount_iban']) ? $values['bankAccount_iban'] : null);
        $bankAccount->setSwift(isset($values['bankAccount_swift']) ? $values['bankAccount_swift'] : null);

        // ------------------------------------- Items ---------------------------------------- \\

        // Remove items
        foreach ($taxDocument->getLineItems() as $lineItem) {
            $taxDocument->removeLineItem($lineItem);
            //
            $this->entityManager->remove($lineItem);
        }

        // Items
        if(isset($httpData['lineItems'])) {
            foreach ($httpData['lineItems'] as $_lineItem) {
                $lineItem = new LineItem();
                $lineItem->setName($_lineItem['name']);
                $lineItem->setQuantity((int)$_lineItem['quantity']);
                $lineItem->setUnit($_lineItem['unit']);
                $lineItem->setType('line_item');
                // Price + Tax rate
                $lineItem->setTaxRate($_lineItem['taxRate']);
                $lineItem->setUnitPriceTaxExcl($_lineItem['unitPriceTaxExcl']);
                //
                $taxDocument->addLineItem($lineItem);
            }
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
        if ($this->taxDocument) {
            $this->presenter->flashMessage('Doklad bol úspešne aktualizovaný', 'success');
        } else {
            $this->presenter->flashMessage('Doklad bol úspešne vytvorený', 'success');
        }
        //

        if($this->getUserCompany()) {
            $this->presenter->redirect(':TaxDocument:List:userCompany', [
                'id' => $this->getUserCompany()->getId()
            ]);
        } else {
            $this->presenter->redirect(':TaxDocument:List:default');
        }
    }

    // ------------------------------------ Helpers ---------------------------------- \\

    public function setTaxDocument(?TaxDocument $taxDocument): void
    {
        $this->taxDocument = $taxDocument;
    }

    public function setUserCompany(?UserCompany $userCompany): void
    {
        $this->userCompany = $userCompany;
    }

    public function getUserCompany()
    {
        if($this->userCompany) {
            return $this->userCompany;
        }

        if($this->taxDocument) {
            return $this->taxDocument->getUserCompany();
        }

        return null;
    }

    private function setDefaults(Form $form): void
    {
        $defaults = array();

        if ( ! $this->taxDocument && $this->userCompany) {
            $company        = $this->userCompany;
            $billingAddress = $company->getBillingAddress();
            $bankAccount    = $company->getBankAccount();
            //
            $defaults = array_merge($defaults, array(
                'type'                => TaxDocument::TYPE_INVOICE,
                'userCompany'         => $company->getId(),
                //
                'supplier_name'       => $company->getName(),
                'supplier_businessId' => $billingAddress ? $billingAddress->getBusinessId() : null,
                'supplier_taxId'      => $billingAddress ? $billingAddress->getTaxId() : null,
                'supplier_vatNumber'  => $billingAddress ? $billingAddress->getVatNumber() : null,
                'supplier_phone'          => $billingAddress ? $billingAddress->getPhone() : null,
                'supplier_email'          => $billingAddress ? $billingAddress->getEmail() : null,
                'supplier_street'         => $billingAddress ? $billingAddress->getStreet() : null,
                'supplier_city'           => $billingAddress ? $billingAddress->getCity() : null,
                'supplier_zipCode'        => $billingAddress ? $billingAddress->getZipCode() : null,
                'supplier_countryCode'    => $billingAddress ? $billingAddress->getCountryCode() : null,
                // Bank
                'paymentData_paypalMail'  => $company->getPaypalEmail(),
                'paymentData_bankAccount' => $bankAccount ? $bankAccount->getAccountNumber() : null,
                'paymentData_iban'        => $bankAccount ? $bankAccount->getIban() : null,
                'paymentData_swift'       => $bankAccount ? $bankAccount->getSwift() : null,
            ));

            // Bank account
            if ($company->getBankAccount()) {
                $bankAccount = $company->getBankAccount();

                $defaults = array_merge($defaults, array(
                    // Company
                    'bankAccount_accountNumber' => $bankAccount->getAccountNumber(),
                    'bankAccount_iban'          => $bankAccount->getIban(),
                    'bankAccount_swift'         => $bankAccount->getSwift(),
                ));
            }
        }

        if ($this->taxDocument) {
            $entity = $this->taxDocument;
            //
            $defaults = array_merge($defaults, array(
                'contact'        => $entity->getContact() ? $entity->getContact()->getId() : null,
                'type'           => $entity->getType(),
                'number'         => $entity->getNumber(),
                'evidenceNumber' => $entity->getEvidenceNumber(),
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
            if ($entity->getPaymentData()) {
                $paymentData = $entity->getPaymentData();
                //
                $defaults = array_merge($defaults, array(
                    'paymentData_type'        => $paymentData->getType(),
                    'paymentData_paypalMail'  => $paymentData->getPaypalMail(),
                    'paymentData_bankAccount' => $paymentData->getBankAccountNumber(),
                    'paymentData_iban'        => $paymentData->getBankAccountIban(),
                    'paymentData_swift'       => $paymentData->getBankAccountSwift(),
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
            ->find((int)$id);

        if ( ! $company) {
            $this->error();
        }


        //
        $billingAddress = $company->getBillingAddress();
        $bankAccount    = $company->getBankAccount();

        // Send data
        $this->presenter->sendJson(array(
            'supplier_name'           => $company->getName(),
            'supplier_businessId'     => $billingAddress ? $billingAddress->getBusinessId() : null,
            'supplier_taxId'          => $billingAddress ? $billingAddress->getTaxId() : null,
            'supplier_vatNumber'      => $billingAddress ? $billingAddress->getVatNumber() : null,
            'supplier_phone'          => $billingAddress ? $billingAddress->getPhone() : null,
            'supplier_email'          => $billingAddress ? $billingAddress->getEmail() : null,
            'supplier_street'         => $billingAddress ? $billingAddress->getStreet() : null,
            'supplier_city'           => $billingAddress ? $billingAddress->getCity() : null,
            'supplier_zipCode'        => $billingAddress ? $billingAddress->getZipCode() : null,
            'supplier_countryCode'    => $billingAddress ? $billingAddress->getCountryCode() : null,
            // Bank
            'paymentData_bankAccount' => $bankAccount ? $bankAccount->getAccountNumber() : null,
            'paymentData_iban'        => $bankAccount ? $bankAccount->getIban() : null,
            'paymentData_swift'       => $bankAccount ? $bankAccount->getSwift() : null,
            // PayPal
            'paymentData_paypalMail' => $company->getPaypalEmail() ?: null
        ));
    }

    public function handleLoadContactData(): void
    {
        $id = $this->presenter->request->getPost('id');
        //
        /** @var Contact|null $company */
        $contact = $this->entityManager
            ->getRepository(Contact::class)
            ->find((int)$id);

        if ( ! $contact) {
            $this->error();
        }

        //
        $billingAddress = $contact->getBillingAddress();
        $bankAccount    = $contact->getBankAccount();

        // Send data
        $this->presenter->sendJson(array(
            'subscriber_name'           => $contact->getName(),
            'subscriber_businessId'     => $billingAddress ? $billingAddress->getBusinessId() : null,
            'subscriber_taxId'          => $billingAddress ? $billingAddress->getTaxId() : null,
            'subscriber_vatNumber'      => $billingAddress ? $billingAddress->getVatNumber() : null,
            'subscriber_phone'          => $billingAddress ? $billingAddress->getPhone() : null,
            'subscriber_email'          => $billingAddress ? $billingAddress->getEmail() : null,
            'subscriber_street'         => $billingAddress ? $billingAddress->getStreet() : null,
            'subscriber_city'           => $billingAddress ? $billingAddress->getCity() : null,
            'subscriber_zipCode'        => $billingAddress ? $billingAddress->getZipCode() : null,
            'subscriber_countryCode'    => $billingAddress ? $billingAddress->getCountryCode() : null,
            // Bank
            'paymentData_bankAccount' => $bankAccount ? $bankAccount->getAccountNumber() : null,
            'paymentData_iban'        => $bankAccount ? $bankAccount->getIban() : null,
            'paymentData_swift'       => $bankAccount ? $bankAccount->getSwift() : null,
        ));
    }


    /**
     * @return TaxDocument|null
     */
    protected function getLastTaxDocument()
    {
        if($this->taxDocument) {
            return null;
        }

        $qb = $this->entityManager
            ->getRepository(TaxDocument::class)
            ->createQueryBuilder('td');

        $td = $qb->select('td')
                 ->where('td.userCompany = :userCompany')
                 ->setParameter('userCompany', $this->userCompany)
                 ->setMaxResults(1)
                 ->orderBy('td.createdAt', 'DESC')
                 ->getQuery()
                 ->getResult();

        if(isset($td[0])) {
            return $td[0];
        } else {
            return null;
        }
    }

    protected function checkIfNumberExists($number): bool
    {
        $qb = $this->entityManager
            ->getRepository(TaxDocument::class)
            ->createQueryBuilder('td');

        $td = $qb->select('td')
                 ->where('td.userCompany = :userCompany')
                 ->setParameter('userCompany', $this->userCompany)
                 ->andWhere('td.number = :number')
                 ->setParameter('number', $number)
                 ->setMaxResults(1)
                 ->getQuery()
                 ->getResult();

        if(isset($td[0])) {
            return true;
        } else {
            return false;
        }
    }

    protected function checkIfEvidenceNumberExists($number): bool
    {
        $qb = $this->entityManager
            ->getRepository(TaxDocument::class)
            ->createQueryBuilder('td');

        $td = $qb->select('td')
                 ->where('td.userCompany = :userCompany')
                 ->setParameter('userCompany', $this->userCompany)
                 ->andWhere('td.evidenceNumber = :number')
                 ->setParameter('number', $number)
                 ->setMaxResults(1)
                 ->getQuery()
                 ->getResult();

        if(isset($td[0])) {
            return true;
        } else {
            return false;
        }
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