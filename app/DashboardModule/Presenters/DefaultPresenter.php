<?php
declare(strict_types=1);

namespace App\DashboardModule\Presenters;

use App\Presenters\BasePresenter;

class DefaultPresenter extends BasePresenter
{
    public function actionDefault()
    {
        $this->redirect(':TaxDocument:List:default');
    }

}