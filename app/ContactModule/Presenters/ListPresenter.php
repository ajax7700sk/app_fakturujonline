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
            ->createQueryBuilder('contact')
            ->leftJoin('\App\Entity\Address', 'billingAddress', Join::WITH, 'contact.billingAddress = billingAddress.id')
            ->leftJoin('\App\Entity\Address', 'shippingAddress', Join::WITH, 'contact.shippingAddress = shippingAddress.id')
            ->innerJoin('\App\Entity\User', 'user', Join::WITH, 'contact.user = user.id')
        ;

        $grid = new DataGrid($this, $name);
//        $grid->setStrictSessionFilterValues();
        //$grid->setRememberState(false);
//        $grid->setColumnsHideable();
        //set grid data source
        $grid->setDataSource($data);
//        $grid->setDefaultSort(['created' => 'DESC']);

        //grid columns
//        $grid->addColumnNumber('id', 'ID', 'id')
//             ->setSortable()
//             ->setFilterText()
//             ->setExactSearch(true);
        $grid->addColumnText('name', 'Názov', 'name')
             ->setFilterText();
        $grid->addColumnText('billingAddressName', 'Názov spoločnosti', 'billingAddress.name')
             ->setFilterText();
        $grid->addColumnText('billingAddressBusinessId', 'IČO', 'billingAddress.businessId')
             ->setFilterText();
        $grid->addColumnText('billingAddressCity', 'Město', 'billingAddress.city')
             ->setFilterText();
        $grid->addColumnDateTime('createdAt', 'Vytvorené', 'createdAt')
            ->setFilterDateRange();
        $grid->addColumnDateTime('updatedAt', 'Upravené', 'updatedAt')
            ->setFilterDateRange();

        $grid->setOuterFilterRendering(true);
        //set translator
        $grid->setTranslator($this->translator);


        return $grid;
    }
}