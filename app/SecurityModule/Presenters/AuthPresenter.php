<?php
declare(strict_types=1);

namespace App\SecurityModule\Presenters;

use App\Entity\User;
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

    /** @var User|null */
    private $newPasswordFormUser;


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

    public function actionNewPassword($token)
    {
        /** @var User|null $user */
        $user = $this->em
            ->getRepository(User::class)
            ->findOneBy([
                'resetToken' => $token
            ]);
        //

        if(!$user) {
            $this->flashMessage('Zadaný token neexistuje', 'danger');
            //
            $this->redirect(':Security:Auth:login');
        }

        // Is token valid
        if($user->getResetTokenValidAt() < (new \DateTime())) {
            $this->flashMessage('Platnosť tokenu vypršala', 'danger');
            //
            $this->redirect(':Security:Auth:login');
        }

        //
        $this->newPasswordFormUser = $user;
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
        /** @var NewPasswordForm $control */
        $control = $this->newPasswordForm->create();
        $control->user = $this->newPasswordFormUser;

        return $control;
    }

    public function createComponentRegisterForm(): RegisterForm
    {
        /** @var RegisterForm $control */
        $control = $this->registerForm->create();

        return $control;
    }
}