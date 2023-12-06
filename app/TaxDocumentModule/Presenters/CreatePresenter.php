<?php
declare(strict_types=1);

namespace App\TaxDocumentModule\Presenters;

use App\Entity\UserCompany;

class CreatePresenter extends BasePresenter
{

    public function actionDefault($id)
    {
        /** @var UserCompany|null $userCompany */
        $userCompany = $this->em->getRepository(UserCompany::class)->find((int) $id);

        if(!$userCompany) {
            $this->error();
        }

        $this->taxDocumentFormUserCompany = $userCompany;
        //
        $this->template->userCompany = $userCompany;
    }
}