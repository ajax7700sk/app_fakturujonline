<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Ecommerce\Order;
use App\Entity\Ecommerce\Subscription;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    /** @var EntityManagerInterface @inject */
    public $em;

    /** @var SubscriptionService @inject */
    public $subscriptionService;

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

    public function hasUserActiveSubscription(?User $user): bool
    {
        if(!$user) {
            return false;
        }

        $qb = $this->em
            ->getRepository(Subscription::class)
            ->createQueryBuilder('s');

        $qb
            ->select('s')
            ->where('s.startAt < :now')
            ->andWhere('s.endAt >= :now')
            ->setParameter('now', new \DateTime())
            ->setMaxResults(1);

        $subscriptions = $qb->getQuery()->getResult();

        return count($subscriptions) > 0 ? true : false;
    }

    private function createSubscriptionFromOrder(Order $order): Subscription
    {
        return $this->subscriptionService->createSubscriptionFromOrder($order);
    }


}