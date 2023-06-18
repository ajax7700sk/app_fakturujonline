<?php
declare(strict_types=1);

namespace App\TaxDocumentModule\Presenters;

use App\TaxDocumentModule\Forms\ITaxDocumentForm;
use App\TaxDocumentModule\Forms\TaxDocumentForm;

class BasePresenter extends \App\Presenters\BasePresenter
{
    /** @var ITaxDocumentForm @inject */
    public $taxDocumentForm;

    /*********************************************************************
     * Components
     ********************************************************************/

    public function createComponentTaxDocumentForm(): TaxDocumentForm
    {
        /** @var TaxDocumentForm $control */
        $control = $this->taxDocumentForm->create();

        //

        return $control;
    }
}