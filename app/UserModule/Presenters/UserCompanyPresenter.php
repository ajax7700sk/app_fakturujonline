<?php
declare(strict_types=1);

namespace App\UserModule\Presenters;

use App\Entity\UserCompany;
use App\UserModule\Forms\IUserCompanyForm;
use App\UserModule\Forms\UserCompanyForm;

class UserCompanyPresenter extends BasePresenter
{
    /** @var IUserCompanyForm @inject */
    public $userCompanyForm;

    // --- Form fields

    /** @var UserCompany */
    private $userCompanyFormUserCompany;

    public function actionCreate()
    {
        //
    }

    public function actionEdit($id)
    {
        $entity = $this->em->getRepository(UserCompany::class)->find((int) $id);

        if(!$entity) {
            // 404
            $this->error();
        }

        //
        $this->userCompanyFormUserCompany = $entity;
    }

    public function actionDelete($id)
    {
        dd($id);
    }

    /*********************************************************************
     * Components
     ********************************************************************/

    public function createComponentUserCompanyForm(): UserCompanyForm
    {
        /** @var UserCompanyForm $control */
        $control = $this->userCompanyForm->create();
        $control->setUserCompany($this->userCompanyFormUserCompany);

        return $control;
    }
}