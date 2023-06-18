<?php
declare(strict_types=1);

namespace App\ContactModule\Presenters;

use App\Entity\Contact;

class EditPresenter extends BasePresenter
{
    public function actionDefault($id)
    {
        /** @var Contact|null $contact */
        $contact = $this->em
            ->getRepository(Contact::class)
            ->find((int)$id);
        //

        if ( ! $contact) {
            $this->error();
        }

        $this->contactFormContact = $contact;
    }
}