<?php
declare(strict_types=1);

namespace App\UserModule\Presenters;

use App\UserModule\Forms\IUserCompanyForm;
use App\UserModule\Forms\UserCompanyForm;

class UserCompanyPresenter extends BasePresenter
{
    /** @var IUserCompanyForm @inject */
    public $userCompanyForm;

    public function actionCreate()
    {
        //
    }

    /*********************************************************************
     * Components
     ********************************************************************/

    public function createComponentUserCompanyForm(): UserCompanyForm
    {
        /** @var UserCompanyForm $control */
        $control = $this->userCompanyForm->create();

        return $control;
    }
}