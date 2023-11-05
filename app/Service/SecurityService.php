<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class SecurityService
{
    /** @var EntityManagerInterface @inject */
    public $em;

    /** @var EmailService @inject */
    public $emailService;

    /** @var SubscriptionService @inject */
    public $subscriptionService;

    public function registerUser(User $user): User
    {
        // Persist
        $this->em->persist($user);
        $this->em->flush();

        // Create 1 month days access
        $this->subscriptionService->createSubscriptionAfterRegistration($user);

        // Send some mail?
        try {
            $this->emailService->registration($user);
        } catch (\Exception $e) {
            //
        }

        //
        return $user;
    }
}