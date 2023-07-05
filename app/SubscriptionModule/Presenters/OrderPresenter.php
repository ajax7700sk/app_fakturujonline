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

    public function actionSelectType()
    {
        //
    }

    public function actionType($type)
    {
        //
    }

    /*********************************************************************
     * Components
     ********************************************************************/

    public function createComponentCheckoutForm(): CheckoutForm
    {
        /** @var ICheckoutForm $control */
        $control = $this->checkoutForm->create();

        return $control;
    }
}