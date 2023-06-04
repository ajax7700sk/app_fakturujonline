<?php
declare(strict_types=1);

namespace App\SecurityModule\Presenters;

use App\SecurityModule\Forms\ILoginForm;
use App\SecurityModule\Forms\IRegisterForm;
use App\SecurityModule\Forms\IResetPasswordForm;
use App\SecurityModule\Forms\LoginForm;
use App\SecurityModule\Forms\RegisterForm;
use App\SecurityModule\Forms\ResetPasswordForm;

class AuthPresenter extends BasePresenter
{
    /** @var IResetPasswordForm @inject */
    public $resetPasswordForm;

    /** @var ILoginForm @inject */
    public $loginForm;

    /** @var IRegisterForm @inject */
    public $registerForm;


    /*********************************************************************
     * Actions
     ********************************************************************/

    public function actionRegister()
    {
        //
    }

    public function actionLogin()
    {
        //
    }

    public function actionResetPassword()
    {

    }

    /*********************************************************************
     * Components
     ********************************************************************/

    public function createComponentLoginForm(): LoginForm
    {
        /** @var LoginForm $control */
        $control = $this->loginForm->create();

        return $control;
    }

    public function createComponentResetPasswordForm(): ResetPasswordForm
    {
        /** @var ResetPasswordForm $control */
        $control = $this->resetPasswordForm->create();

        return $control;
    }

    public function createComponentRegisterForm(): RegisterForm
    {
        /** @var RegisterForm $control */
        $control = $this->registerForm->create();

        return $control;
    }
}