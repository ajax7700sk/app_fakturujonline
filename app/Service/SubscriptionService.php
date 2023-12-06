<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Ecommerce\Order;
use App\Entity\Ecommerce\Subscription;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionService
{
    /** @var EntityManagerInterface @inject */
    public $em;

    public function createSubscriptionFromOrder(Order $order): Subscription
    {
        $endAt = new \DateTime();
        //
        $subscription = new Subscription();
        $subscription
            ->setOrder($order)
            ->setUser($order->getUser())
            ->setType($order->getSubscriptionType());

        switch ($subscription->getType()) {
            case Subscription::TYPE_MONTH:
                $endAt = $endAt->modify('+1 month');
                break;
            case Subscription::TYPE_QUARTER:
                $endAt = $endAt->modify('+3 months');
                break;
            case Subscription::TYPE_YEAR:
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


    public function createSubscriptionAfterRegistration(User $user): Subscription
    {
        $endAt = new \DateTime();
        //
        $subscription = new Subscription();
        $subscription
            ->setUser($user)
            ->setType(Subscription::TYPE_MONTH);
        //
        $endAt = $endAt->modify('+14 days');

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