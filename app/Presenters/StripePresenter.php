<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Gateways\Stripe;

class StripePresenter extends BasePresenter
{
    private Stripe $stripe;

    public function __construct(Stripe $stripe)
    {
        $this->stripe = $stripe;
    }

    public function processPayment()
    {
        $this->stripe->processPayment();
    }
}