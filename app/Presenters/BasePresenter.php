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

        // --- Can redirect from security to dashboard
        if ($this->canRedirectFromSecurity()) {
            $this->redirect(":Contact:List:default");
        }

        //
        $this->template->currentActionMask = $this->getAction(true);
        $this->template->user = $this->getLoggedUser();
    }

    // ------------------------------------- Helpers -------------------------------------- \\

    protected function canRedirectFromSecurity(): bool
    {
        if($this->getAction(true) == ':Security:Auth:logout') {
            return false;
        }

        // Is not security presenter
        if ( ! in_array($this->name, ['Security:Auth'])) {
            return false;
        }

        if ($this->isLoggedIn()) {
            return true;
        }

        return false;
    }

    protected function isLoggedIn(): bool
    {
        // Is not logged in
        if ( ! $this->getUser()->isLoggedIn()) {
            return false;
        }

        /** @var User|null $user */
        $user = $this->em
            ->getRepository(User::class)
            ->find((int)$this->getUser()->getId());

        // User does not exists
        if ($user) {
            return true;
        }

        return false;
    }

    protected function getLoggedUser(): ?User
    {
        /** @var User|null $user */
        $user = $this->em->getRepository(User::class)->find((int)$this->getUser()->id);

        return $user;
    }
}