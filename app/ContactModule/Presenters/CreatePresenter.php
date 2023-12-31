<?php
declare(strict_types=1);

namespace App\ContactModule\Presenters;

class CreatePresenter extends BasePresenter
{
    public function actionDefault()
    {
        if ( ! $this >> $this->hasActiveSubscription()) {
            $this->error();
        }
    }
}