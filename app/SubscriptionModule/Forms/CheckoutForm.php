<?php
declare(strict_types=1);

namespace App\SubscriptionModule\Forms;

use App\Entity\Ecommerce\Order;
use App\Entity\Ecommerce\OrderItem;
use App\Entity\UserCompany;
use App\Forms\AbstractForm;
use App\Service\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;
use Nette\Localization\Translator;
use Nette\Security\User;
use Symfony\Component\Intl\Countries;

class CheckoutForm extends AbstractForm
{
    private EntityManagerInterface $entityManager;

    /** @var \Nette\Security\User */
    public $securityUser;
    public $type;
    private Translator $translator;
    private PaymentService $paymentService;

    public function __construct(
        EntityManagerInterface $entityManager,
        Translator $translator,
        User $securityUser,
        PaymentService $paymentService,
    ) {
        $this->entityManager  = $entityManager;
        $this->translator     = $translator;
        $this->securityUser   = $securityUser;
        $this->paymentService = $paymentService;
    }

    /**
     * Render a form
     */
    public function render()
    {
        //
        $this->template->render(__DIR__.'./../templates/forms/checkout.latte');
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
        //
        $list = ['-Vybrať-'];

        foreach ($this->getLoggedUser()->getUserCompanies() as $userCompany) {
            $list[$userCompany->getId()] = $userCompany->getName();
        }

        // Form
        $form = new Form();
        $form->setTranslator($this->translator);

        $form->addHidden('type', $this->type);
        $form->addSelect('userCompany', 'Faktúrovať na spoločnosť', $list);
        // Billing address
        $form->addText('billingAddress_name', 'Názov spoločnosti')
             ->setRequired("form.general.validation.required");
        $form->addText('billingAddress_businessId', 'IČO')
             ->setRequired("form.general.validation.required");
        $form->addText('billingAddress_taxId', 'DIČ');
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
        //
        $price   = OrderItem::priceByType($values['type']);
        $taxRate = Order::TAX_RATE_DEFAULT;

        // --- Create order
        $order = new Order();
        //
        $orderItem = new OrderItem();
        $orderItem
            ->setType($values['type'])
            ->setName('Predplatné')
            ->setQuantity(1)
            ->setTaxRate((string)$taxRate)
            ->setUnitPriceTaxExcl((string)$price)
            ->setTotalPriceTaxExcl((string)($price * 1))
            ->setUnitTaxTotal((string)($price * ($taxRate / 100)))
            ->setTotalTax((string)($orderItem->getTotalPriceTaxExcl() * ($taxRate / 100)));

        $order->addItem($orderItem);
        //
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        // --- Create payment
        $payment = $this->paymentService->createPaymentFromOrder($order, 'stripe');
        //

        // --- Create stripe payment
        $data = $this->paymentService->createStripePaymentFromPayment(
            $payment,
            $this->presenter->link('//:TaxDocument:Order:stripeSuccess'),
            $this->presenter->link('//:TaxDocument:Order:stripeCancel')
        );

        // Redirect
        $this->presenter->redirectUrl($data['url']);
    }

    // ------------------------------------ Helpers ---------------------------------- \\

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

        // Send data
        $this->presenter->sendJson(array(
            'billingAddress_name'        => $company->getName(),
            'billingAddress_businessId'  => $billingAddress ? $billingAddress->getBusinessId() : null,
            'billingAddress_taxId'       => $billingAddress ? $billingAddress->getTaxId() : null,
            'billingAddress_vatNumber'   => $billingAddress ? $billingAddress->getVatNumber() : null,
            'billingAddress_phone'       => $billingAddress ? $billingAddress->getPhone() : null,
            'billingAddress_email'       => $billingAddress ? $billingAddress->getEmail() : null,
            'billingAddress_street'      => $billingAddress ? $billingAddress->getStreet() : null,
            'billingAddress_city'        => $billingAddress ? $billingAddress->getCity() : null,
            'billingAddress_zipCode'     => $billingAddress ? $billingAddress->getZipCode() : null,
            'billingAddress_countryCode' => $billingAddress ? $billingAddress->getCountryCode() : null,
        ));
    }

}

/**
 * Interface ITaxDocumentForm
 *
 * @package App\Forms
 */
interface ICheckoutForm
{
    public function create(): CheckoutForm;
}