<?php
declare(strict_types=1);

namespace App\SubscriptionModule\Presenters;

use App\SubscriptionModule\Forms\CheckoutForm;
use App\SubscriptionModule\Forms\ICheckoutForm;
use App\TaxDocumentModule\Forms\TaxDocumentForm;

class OrderPresenter extends BasePresenter
{
    /** @var ICheckoutForm @inject */
    public $checkoutForm;
    private $checkoutType;

    public function actionSelectType()
    {
        //
    }

    public function actionType($type)
    {
        $this->checkoutType = $type;
    }

    // --- Gateways

    public function actionStripeSuccess()
    {
        dd("Success");
    }

    public function actionStripeCancel()
    {
        dd("Cancel");
    }

    /*********************************************************************
     * Components
     ********************************************************************/

    public function createComponentCheckoutForm(): CheckoutForm
    {
        /** @var ICheckoutForm $control */
        $control = $this->checkoutForm->create();
        $control->type = $this->checkoutType;

        return $control;
    }
}