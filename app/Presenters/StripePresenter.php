<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Gateways\Stripe;

class StripePresenter extends ApplicationPresenter
{
    private Stripe $stripe;

    public function __construct(Stripe $stripe)
    {
        $this->stripe = $stripe;
    }

    public function actionProcessPayment()
    {
        $this->stripe->processPayment();
    }
}