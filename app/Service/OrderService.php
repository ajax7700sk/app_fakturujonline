<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Ecommerce\Order;
use App\Entity\Ecommerce\Subscription;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    /** @var EntityManagerInterface @inject */
    public $em;

    public function setOrderAsPaid(Order $order): Order
    {
        if ($order->getState() == 'paid') {
            return $order;
        }

        // Update
        $order->setState('paid');
        //
        $this->em->flush();
        // TODO: maybe send mail?
        $this->createSubscriptionFromOrder($order);

        return $order;
    }

    public function setOrderAsUnPaid(Order $order): Order
    {
        if ($order->getState() == 'unpaid') {
            return $order;
        }

        // Update
        $order->setState('unpaid');
        //
        $this->em->flush();

        return $order;
    }

    private function createSubscriptionFromOrder(Order $order): Subscription
    {
        $endAt = new \DateTime();
        //
        $subscription = new Subscription();
        $subscription->setOrder($order)
                     ->setUser($order->getUser())
                     ->setType($order->getSubscriptionType());

        switch ($subscription->getType()) {
            case 'month':
                $endAt = $endAt->modify('+1 month');
                break;
            case 'quarter':
                $endAt = $endAt->modify('+3 months');
                break;
            case 'year':
                $endAt = $endAt->modify('+1 year');
                break;
        }

        // Create start and end for subscription
        $subscription->setStartAt(new \DateTime());
        $subscription->setEndAt($endAt);

        //
        $this->em->persist($subscription);
        $this->em->flush();;
        //

        return $subscription;
    }

}