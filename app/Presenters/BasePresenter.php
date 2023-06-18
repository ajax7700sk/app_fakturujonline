<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Nette;
use Nette\Localization\Translator;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var EntityManagerInterface @inject */
    public $em;

    /** @var Translator @inject */
    public $translator;

    public function startup()
    {
        parent::startup();

        // --- Check if user is logged in
//        if ($this->canRedirectToLogin()) {
//            $this->redirect(":Security:Auth:login");
//        }

    }

    // ------------------------------------- Helpers -------------------------------------- \\

    protected function canRedirectToLogin(): bool
    {
        if ($this->getUser()->isLoggedIn()) {
            return false;
        }

        // Login or register
        if (in_array($this->name, ['Security:Auth'])) {
            return false;
        }

        return true;
    }

    protected function getLoggedUser(): ?User
    {
        /** @var User|null $user */
        $user = $this->em->getRepository(User::class)->find((int)$this->getUser()->id);

        return $user;
    }
}