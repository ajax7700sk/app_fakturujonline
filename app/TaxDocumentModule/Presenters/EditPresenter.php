<?php
declare(strict_types=1);

namespace App\TaxDocumentModule\Presenters;

use App\Entity\TaxDocument;

class EditPresenter extends BasePresenter
{
    public function actionDefault($id)
    {
        if(!$this>>$this->hasActiveSubscription()) {
            $this->error();
        }

        /** @var TaxDocument|null $taxDocument */
        $taxDocument = $this->em
            ->getRepository(TaxDocument::class)
            ->find((int)$id);
        //

        if ( ! $taxDocument) {
            $this->error();
        }

        $this->taxDocumentFormTaxDocument = $taxDocument;
    }
}