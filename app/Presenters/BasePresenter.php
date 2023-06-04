<?php
declare(strict_types=1);

namespace App\Presenters;

use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    public function startup()
    {
        parent::startup();

        // --- Check if user is logged in
        if ($this->canRedirectToLogin()) {
            $this->redirect(":Security:Login:default");
        }

    }

    // ------------------------------------- Helpers -------------------------------------- \\

    protected function canRedirectToLogin(): bool
    {
        if ($this->getUser()->isLoggedIn()) {
            return false;
        }

        // Login or register
        if (in_array($this->name, ['Security:Login', 'Security:Register'])) {
            return false;
        }

        return true;
    }
}