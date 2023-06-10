<?php
declare(strict_types=1);

namespace App\ContactModule\Presenters;

use App\Entity\Contact;
use App\Entity\Interfaces\ITaxDocument;
use App\Entity\Invoice;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Ublaboo\DataGrid\DataGrid;

class ListPresenter extends BasePresenter
{
    public function actionDefault()
    {
        //
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
    protected function createComponentContactGrid(string $name)
    {
        /** @var ContactRepository $repository */
        $repository = $this->em->getRepository(Contact::class);

        $data = $repository
            ->createQueryBuilder('c')
//            ->leftJoin('\App\Entity\Address', 'ba', Join::WITH, 'c.billingAddress = ba.id')
//            ->leftJoin('\App\Entity\Address', 'sa', Join::WITH, 'c.shippingAddress = sa.id')
            ->innerJoin('\App\Entity\User', 'u', Join::WITH, 'c.user = u.id')
        ;

        $grid = new DataGrid($this, $name);
//        $grid->setStrictSessionFilterValues();
        //$grid->setRememberState(false);
//        $grid->setColumnsHideable();
        //set grid data source
        $grid->setDataSource($data);
//        $grid->setDefaultSort(['created' => 'DESC']);

        //grid columns
        $grid->addColumnNumber('id', 'ID', 'id')
             ->setSortable()
             ->setFilterText()
             ->setExactSearch(true);
        $grid->addColumnText('name', 'Názov', 'name')
             ->setFilterText();
        $grid->addColumnText('firstName', 'Meno', 'u.firstName')
             ->setFilterText();

        $grid->setOuterFilterRendering(true);
        //set translator
        $grid->setTranslator($this->translator);


        return $grid;
    }
}