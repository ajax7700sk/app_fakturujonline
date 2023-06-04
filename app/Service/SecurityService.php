<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class SecurityService
{
    /** @var EntityManagerInterface @inject */
    public $em;

    public function registerUser(User $user): User
    {
        // Persist
        $this->em->persist($user);
        $this->em->flush();

        // Send some mail?

        //
        return $user;
    }
}