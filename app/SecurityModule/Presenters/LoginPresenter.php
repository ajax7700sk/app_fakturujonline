<?php
declare(strict_types=1);

namespace App\SecurityModule\Presenters;

use App\SecurityModule\Forms\ILoginForm;
use App\SecurityModule\Forms\LoginForm;

class LoginPresenter extends BasePresenter
{
    /** @var ILoginForm @inject */
    public $loginForm;

    /*********************************************************************
     * Actions
     ********************************************************************/

    public function actionDefault()
    {
        //
    }

    /*********************************************************************
     * Components
     ********************************************************************/

    public function createComponentLoginForm(): LoginForm
    {
        $control = $this->loginForm->create();

        return $control;
    }
}