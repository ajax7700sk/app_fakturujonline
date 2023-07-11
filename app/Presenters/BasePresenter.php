<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Entity\User;
use App\SecurityModule\Presenters\AuthPresenter;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Nette;
use Nette\Localization\Translator;

abstract class BasePresenter extends ApplicationPresenter
{
    /** @var OrderService @inject */
    public $orderService;

    public function startup()
    {
        parent::startup();


        if(!$this->isLoggedIn() && get_class($this->getPresenter()) != AuthPresenter::class) {
            $this->redirect(':Security:Auth:login');
        }

        // --- Can redirect from security to dashboard
        if ($this->canRedirectFromSecurity()) {
            $this->redirect(":Contact:List:default");
        }

        //
        $user = $this->getLoggedUser();
        //
        $this->template->currentActionMask = $this->getAction(true);
        $this->template->user = $user;
        $this->template->userCompanies = $user ? $user->getUserCompanies() : [];
        $this->template->hasActiveSubscription = $this->orderService->hasUserActiveSubscription($user);
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