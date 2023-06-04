<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Nette;
use Nette\Security\Passwords;


class Authenticator implements Nette\Security\IAuthenticator
{

    /** @var  \Doctrine\ORM\EntityManagerInterface @inject */
    public $entityManager;

    public function authenticate(array $credentials)
    {
        [$name, $password] = $credentials;

        /** @var UserRepository $repository */
        $repository = $this->entityManager->getRepository(User::class);

        /** @var User|null $row */
        $row = $repository->findOneBy(["email" => $name]);
        $passwords = new Passwords();

        if ( ! $row) {
            throw new Nette\Security\AuthenticationException(
                'Tento email v systému neexistuje.',
                self::IDENTITY_NOT_FOUND
            );
        } elseif ( ! $passwords->verify($password, $row->getPassword())) {
            throw new Nette\Security\AuthenticationException(
                'Zadali ste nesprávné heslo.',
                self::INVALID_CREDENTIAL
            );
        } elseif ($passwords->needsRehash($row->getPassword())) {
            $this->entityManager->persist($row);
            $this->entityManager->flush();
        }

        return new Nette\Security\Identity($row->getId(), $row->getRoles(), array('user' => $row));
    }

}
