<?php
declare(strict_types=1);

namespace App\TaxDocumentModule\Presenters;

use App\Entity\Contact;
use App\Entity\TaxDocument;
use App\Repository\ContactRepository;
use Doctrine\ORM\Query\Expr\Join;
use Ublaboo\DataGrid\DataGrid;

class ListPresenter extends BasePresenter
{
    public function actionDefault()
    {

    }

    /********************************************************************************
     * //                               Components
     ********************************************************************************/

    /**
     * Invoice datagrid
     *
     * @param  string  $name
     * @return DataGrid
     */
    protected function createComponentTaxDocumentGrid(string $name)
    {
        /** @var ContactRepository $repository */
        $repository = $this->em->getRepository(TaxDocument::class);

        $data = $repository
            ->createQueryBuilder('taxDocument')
            ->leftJoin('\App\Entity\Contact', 'contact', Join::WITH, 'taxDocument.contact = contact.id')
            ->leftJoin('\App\Entity\UserCompany', 'userCompany', Join::WITH, 'taxDocument.userCompany = userCompany.id')
        ;

        $grid = new DataGrid($this, $name);
        $grid->setStrictSessionFilterValues();
        $grid->setRememberState(false);
        $grid->setColumnsHideable();
        //set grid data source
        $grid->setDataSource($data);
        $grid->setDefaultSort(['created' => 'DESC']);

        //grid columns
//        $grid->addColumnNumber('id', 'ID', 'id')
//             ->setSortable()
//             ->setFilterText()
//             ->setExactSearch(true);
        $grid->addColumnText('number', 'Číslo', 'number')
             ->setFilterText();
        $grid->addColumnText('type', 'Typ', 'type')
             ->setFilterText();
        $grid->addColumnText('userCompany_name', 'Spoločnosť', 'userCompany.name')
             ->setFilterText();
        $grid->addColumnText('contact_name', 'Kontakt', 'contact.name')
             ->setFilterText();
        $grid->addColumnDateTime('dueDateAt', 'Splatnosť', 'dueDateAt')
             ->setFilterDateRange();
        $grid->addColumnDateTime('createdAt', 'Vytvorené', 'createdAt')
             ->setFilterDateRange();
        $grid->addColumnDateTime('updatedAt', 'Upravené', 'updatedAt')
             ->setFilterDateRange();

        // Actions
        $grid->addAction('edit', 'Upraviť', ':Contact:Edit:', ['id' => 'id'])
             ->setIcon('pencil')
             ->setClass('btn btn-warning btn-sm');
        $grid->addAction('delete', 'Zmazať', ':Contact:List:delete', ['id' => 'id'])
             ->setIcon('trash')
             ->setClass('btn btn-danger btn-sm');

        $grid->setOuterFilterRendering(true);
        //set translator
        $grid->setTranslator($this->translator);


        return $grid;
    }
}