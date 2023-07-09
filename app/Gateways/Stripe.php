<?php
declare(strict_types=1);

namespace App\Gateways;

use App\Entity\Ecommerce\Payment;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;

class Stripe
{
    const ENDPOINT_SECRET = 'whsec_oZA3ClZZmuFGJVVTjPBH3vrvmY0lZEbc';
    const API_KEY = 'sk_test_51KvJ7XFhL4d5ya3cv77O9F85Frx6k3Obzn5cyT1MZh5AeehzYGzBmX82HL371maOD0du0mLGOJ8bEIhJzI3KMLqj00msVS9Wmi';
    private EntityManagerInterface $em;
    private OrderService $orderService;

    public function __construct(EntityManagerInterface $em, OrderService $orderService)
    {
        $this->em = $em;
        $this->orderService = $orderService;
    }

    /**
     * Create stripe payment
     *
     * @param float $price
     * @param string $currencyCode
     * @param string $paymentName
     * @param string $successUrl
     * @param string $cancelUrl
     * @param string $apiKey
     *
     * @return array
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createPayment(
        float $price,
        string $currencyCode,
        string $paymentName,
        string $successUrl,
        string $cancelUrl,
    ): array {
        \Stripe\Stripe::setApiKey(self::API_KEY);
        $stripe = new \Stripe\StripeClient(self::API_KEY);

        // Create prixe object
        $price = $stripe->prices->create([
            'unit_amount'  => $price,
            'currency'     => $currencyCode,
            'product_data' => [
                'name' => $paymentName,
            ],
        ]);

        // Create checkout session
        $checkoutSession = \Stripe\Checkout\Session::create([
            'line_items'  => [
                [
                    # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                    'price'    => $price,
                    'quantity' => 1,
                ],
            ],
            'mode'        => 'payment',
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
        ]);


        // Return data
        return array(
            'url' => $checkoutSession->url,
            'payment_intent' => $checkoutSession->payment_intent
        );
    }

    public function processPayment()
    {
        $payload    = @file_get_contents( 'php://input' );
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event      = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                self::ENDPOINT_SECRET
            );
        } catch ( \UnexpectedValueException $e ) {
            // Invalid payload
            http_response_code( 400 );
            exit($e->getMessage());
        } catch ( \Stripe\Exception\SignatureVerificationException $e ) {
            // Invalid signature
            http_response_code( 400 );
            exit($e->getMessage());
        }

        /** @var array $session */
        $data_object = $event->data->object;
        /** @var string $payment_intent */
        $payment_intent = $data_object['payment_intent'];
        /** @var string $payment_status */
        $payment_status = $data_object['payment_status'];

        // Handle the event
        switch ( $event->type ) {
            // Occurs when a payment intent using a delayed payment method fails.
            case 'checkout.session.async_payment_failed':
                $session = $event->data->object;
            // Occurs when a payment intent using a delayed payment method finally succeeds.
            case 'checkout.session.async_payment_succeeded':
                $data = array(
                    'payment_intent' => $payment_intent,
                    'payment_status' => $payment_status
                );
                break;
            // Occurs when a Checkout Session has been successfully completed.
            case 'checkout.session.completed':
                $data = array(
                    'payment_intent' => $payment_intent,
                    'payment_status' => $payment_status
                );
                break;
            // Occurs when a Checkout Session is expired.
            case 'checkout.session.expired':
                $session = $event->data->object;
            // ... handle other event types
            default:
                http_response_code( 200 );
                exit();
        }

        // Process
        $this->updatePaymentStatus($data['payment_intent'], $data['payment_status']);

        //
        http_response_code( 200 );
        exit();
    }

    private function updatePaymentStatus(?string $intent, ?string $status): void
    {
        /** @var Payment|null $payment */
        $payment = $this->em
            ->getRepository(Payment::class)
            ->findOneBy(['stripePaymentIntent' => $intent]);

        if($payment) {
            switch ($status) {
                case 'paid':
                    $this->orderService->setOrderAsPaid($payment->getOrder());
                    break;
                case 'unpaid':
                    $this->orderService->setOrderAsUnPaid($payment->getOrder());
                    break;
                default:
                    break;
            }
        }
    }
}