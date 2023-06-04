<?php
declare(strict_types=1);

namespace App\SecurityModule\Presenters;

use App\SecurityModule\Forms\IRegisterForm;
use App\SecurityModule\Forms\RegisterForm;

class RegisterPresenter extends BasePresenter
{
    /** @var IRegisterForm @inject */
    public $registerForm;

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

    public function createComponentRegisterForm(): RegisterForm
    {
        /** @var RegisterForm $control */
        $control               = $this->registerForm->create();

        return $control;
    }
}