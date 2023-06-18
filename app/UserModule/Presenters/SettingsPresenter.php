<?php
declare(strict_types=1);

namespace App\UserModule\Presenters;

use App\UserModule\Forms\IUserSettingsForm;
use App\UserModule\Forms\UserSettingsForm;

class SettingsPresenter extends BasePresenter
{
    /** @var IUserSettingsForm @inject */
    public $userSettingsForm;

    public function actionDefault()
    {
        //
    }

    /*********************************************************************
     * Components
     ********************************************************************/

    public function createComponentUserSettingsForm(): UserSettingsForm
    {
        //
        /** @var UserSettingsForm $control */
        $control = $this->userSettingsForm->create();
        $control->setUser($this->getLoggedUser());

        return $control;
    }
}