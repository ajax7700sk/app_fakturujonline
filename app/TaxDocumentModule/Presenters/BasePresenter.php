<?php
declare(strict_types=1);

namespace App\TaxDocumentModule\Presenters;

use App\Entity\TaxDocument;
use App\Entity\UserCompany;
use App\TaxDocumentModule\Forms\ITaxDocumentForm;
use App\TaxDocumentModule\Forms\TaxDocumentForm;

class BasePresenter extends \App\Presenters\BasePresenter
{
    /** @var ITaxDocumentForm @inject */
    public $taxDocumentForm;

    /** @var null|TaxDocument */
    protected $taxDocumentFormTaxDocument = null;

    /** @var null|UserCompany */
    protected $taxDocumentFormUserCompany = null;

    /*********************************************************************
     * Components
     ********************************************************************/

    public function createComponentTaxDocumentForm(): TaxDocumentForm
    {
        /** @var TaxDocumentForm $control */
        $control = $this->taxDocumentForm->create();
        //
        $control->setTaxDocument($this->taxDocumentFormTaxDocument);
        $control->setUserCompany($this->taxDocumentFormUserCompany);

        return $control;
    }
}