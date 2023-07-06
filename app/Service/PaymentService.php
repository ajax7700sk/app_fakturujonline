<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Ecommerce\Order;
use App\Entity\Ecommerce\Payment;
use App\Gateways\Stripe;
use Doctrine\ORM\EntityManagerInterface;

class PaymentService
{
    private EntityManagerInterface $em;
    private Stripe $stripe;

    public function __construct(EntityManagerInterface $entityManager, Stripe $stripe)
    {
        $this->em = $entityManager;
        $this->stripe = $stripe;
    }

    public function createPaymentFromOrder(Order $order, string $paymentMethod): Payment
    {
        $payment = new Payment();
        $payment
            ->setOrder($order)
            ->setCurrencyCode($order->getCurrencyCode())
            ->setState('new')
            ->setPaymentMethod($paymentMethod)
            ->setAmount($order->getTotalPriceTaxIncl());

        $this->em->persist($payment);
        $this->em->flush();

        return $payment;
    }

    // -------------------------------------- Gateways ----------------------------------------- \\

    public function createStripePaymentFromPayment(Payment $payment, string $successUrl, string $cancelUrl): array
    {
        $stripe = new Stripe();
        $data   = $stripe->createPayment(
            (float)$payment->getAmount(),
            $payment->getCurrencyCode(),
            'Predplatné "fakturujonline.sk"',
            $successUrl,
            $cancelUrl
        );

        return $data;
    }
}