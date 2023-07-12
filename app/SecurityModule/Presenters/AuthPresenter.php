<?php
declare(strict_types=1);

namespace App\SecurityModule\Presenters;

use App\SecurityModule\Forms\ILoginForm;
use App\SecurityModule\Forms\INewPasswordForm;
use App\SecurityModule\Forms\IRegisterForm;
use App\SecurityModule\Forms\IResetPasswordForm;
use App\SecurityModule\Forms\LoginForm;
use App\SecurityModule\Forms\NewPasswordForm;
use App\SecurityModule\Forms\RegisterForm;
use App\SecurityModule\Forms\ResetPasswordForm;

class AuthPresenter extends BasePresenter
{
    /** @var IResetPasswordForm @inject */
    public $resetPasswordForm;

    /** @var INewPasswordForm @inject */
    public $newPasswordForm;

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

    public function actionNewPassword()
    {
        //
    }

    public function actionLogout()
    {
        $this->getUser()->logout();

        $this->flashMessage('Boli ste úspešne odhlásený','success');
        //
        $this->redirect(':Security:Auth:login');
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

    public function createComponentNewPasswordForm(): NewPasswordForm
    {
        $control = $this->newPasswordForm->create();

        return $control;
    }

    public function createComponentRegisterForm(): RegisterForm
    {
        /** @var RegisterForm $control */
        $control = $this->registerForm->create();

        return $control;
    }
}