<?php
declare(strict_types=1);

namespace App\UserModule\Forms;

use App\Entity\Address;
use App\Entity\BankAccount;
use App\Entity\Contact;
use App\Entity\UserCompany;
use App\Exception\FileUploadException;
use App\Forms\AbstractForm;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;
use Nette\Http\FileUpload;
use Nette\Localization\Translator;
use Nette\Security\User;
use App\Intl\Countries;

class UserCompanyForm extends AbstractForm
{
    private EntityManagerInterface $entityManager;

    /** @var \Nette\Security\User */
    public $securityUser;
    private Translator $translator;
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
        $this->template->render(__DIR__.'/../templates/forms/user-company.latte');
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

        if ($this->userCompany) {
            $form->addHidden('id', 'ID');
        }
        //
        $form->addText('name', 'Názov')
             ->setRequired("Pole je povinné");
        $form->addCheckbox('vatPayer', 'Plátca DPH');

        //
        $form->addUpload('logo', 'Logo spoločnosti');
        // Billing address
        $form->addText('billingAddress_name', 'Názov spoločnosti')
             ->setRequired("Pole je povinné");
        $form->addText('billingAddress_businessId', 'IČO')
             ->setRequired("Pole je povinné");
        $form->addText('billingAddress_taxId', 'DIČ');
        $form->addText('billingAddress_vatNumber', 'IČ DPH');
        $form->addText('billingAddress_phone', 'Telefon')
             ->setRequired("Pole je povinné");
        $form->addText('billingAddress_email', 'E-mail')
             ->setRequired("Pole je povinné");
        $form->addText('billingAddress_street', 'Adresa')
             ->setRequired("Pole je povinné");
        $form->addText('billingAddress_city', 'Město')
             ->setRequired("Pole je povinné");
        $form->addText('billingAddress_zipCode', 'PŠC')
             ->setRequired("Pole je povinné");
        $form->addSelect('billingAddress_countryCode', 'Štát', Countries::getNames())
             ->setRequired("Pole je povinné");
        $form->addTextArea('registerInfo', 'Spoločnosť je zapísaná v obchodnom... / Živnostník je zapísaný v živnostenskom registri')
             ->setHtmlAttribute('class', 'form-control')
             ->setRequired("Pole je povinné");

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

        // ------------------------------------- User company ---------------------------------------- \\

        if ( ! $this->userCompany) {
            $userCompany = new UserCompany();
        } else {
            $userCompany = $this->userCompany;
        }

        //
        $userCompany->setUser($this->getLoggedUser());
        $userCompany->setName($values['name']);
        $userCompany->setVatPayer($values['vatPayer']);

        // ------------------------------------- Bank account ---------------------------------------- \\

        if ( ! $this->userCompany && ! $userCompany->getBankAccount()) {
            $bankAccount = new BankAccount();
        } else {
            $bankAccount = $userCompany->getBankAccount();
        }

        $bankAccount->setAccountNumber($values['bankAccount_accountNumber']);
        $bankAccount->setIban($values['bankAccount_iban']);
        $bankAccount->setSwift($values['bankAccount_swift']);
        //
        $userCompany->setBankAccount($bankAccount);

        // ------------------------------------- Billing address ---------------------------------------- \\

        if ( ! $this->userCompany && ! $userCompany->getBillingAddress()) {
            $billingAddress = new Address();
        } else {
            $billingAddress = $userCompany->getBillingAddress();
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

        // Set relations
        $userCompany->setRegisterInfo($values['registerInfo']);
        $userCompany->setBillingAddress($billingAddress);
        $userCompany->setBankAccount($bankAccount);

        // Persist & flush
        $this->entityManager->persist($billingAddress);
        $this->entityManager->persist($bankAccount);
        $this->entityManager->persist($userCompany);
        //
        $this->entityManager->flush();

        // -- Upload logo
        /** @var FileUpload $logo */
        $logo = $values['logo'];

        if($logo && $logo->hasFile()) {
            //
            $filename = $this->uploadLogo($logo, $userCompany);
            $userCompany->setLogo($filename);
            //
            $this->entityManager->flush();
        }

        // Redirect to dashboard
        if ( ! $this->userCompany) {
            $this->presenter->flashMessage('Firma bola úspešne vytvorená', 'success');
        } else {
            $this->presenter->flashMessage('Zmeny boli úspešne uložené', 'success');
        }
        //
        $this->presenter->redirect(':User:Settings:default');
    }

    // ------------------------------------ Helpers ---------------------------------- \\

    public function setUserCompany(?UserCompany $userCompany): void
    {
        $this->userCompany = $userCompany;
    }

    private function setDefaults(Form $form): void
    {
        $defaults = array();

        if ($this->userCompany) {
            $entity = $this->userCompany;
            //
            $defaults = array_merge($defaults, array(
                // Company
                'name'     => $entity->getName(),
                'vatPayer' => $entity->getVatPayer(),
                'registerInfo' => $entity->getRegisterInfo()
            ));

            // Bank account
            if ($entity->getBankAccount()) {
                $bankAccount = $entity->getBankAccount();

                $defaults = array_merge($defaults, array(
                    // Company
                    'bankAccount_accountNumber' => $bankAccount->getAccountNumber(),
                    'bankAccount_iban'          => $bankAccount->getIban(),
                    'bankAccount_swift'      => $bankAccount->getSwift(),
                ));
            }

            // Billing address
            if ($entity->getBillingAddress()) {
                $billingAddress = $entity->getBillingAddress();

                $defaults = array_merge($defaults, array(
                    // Company
                    'billingAddress_name'        => $billingAddress->getName(),
                    'billingAddress_businessId'  => $billingAddress->getBusinessId(),
                    'billingAddress_taxId'       => $billingAddress->getTaxId(),
                    'billingAddress_vatNumber'   => $billingAddress->getVatNumber(),
                    'billingAddress_phone'       => $billingAddress->getPhone(),
                    'billingAddress_email'       => $billingAddress->getEmail(),
                    'billingAddress_street'      => $billingAddress->getStreet(),
                    'billingAddress_city'        => $billingAddress->getCity(),
                    'billingAddress_zipCode'     => $billingAddress->getZipCode(),
                    'billingAddress_countryCode' => $billingAddress->getCountryCode(),
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

    public function uploadLogo(FileUpload $fileUpload, UserCompany $userCompany)
    {
        $filename = sprintf('/media/user-company/logo/%s.jpg', $userCompany->getId());
        $filepath = get_app_www_folder_path() . $filename;
        // Save file
        $fileUpload->move($filepath);

        return $filename;
    }
}

/**
 * Interface IContactForm
 *
 * @package App\Forms
 */
interface IUserCompanyForm
{
    public function create(): UserCompanyForm;
}