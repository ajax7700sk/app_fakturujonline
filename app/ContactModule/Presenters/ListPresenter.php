<?php
declare(strict_types=1);

namespace App\ContactModule\Presenters;

use App\Entity\Contact;
use App\Entity\Interfaces\ITaxDocument;
use App\Entity\Invoice;
use App\Repository\ContactRepository;
use Doctrine\ORM\Query\Expr\Join;
use Ublaboo\DataGrid\DataGrid;

class ListPresenter extends BasePresenter
{
    public function actionDefault()
    {
        //
    }

    public function actionDelete($id)
    {
        /** @var Contact|null $contact */
        $contact = $this->em->getRepository(Contact::class)->find((int) $id);

        if(!$contact) {
            $this->error();
        }

        $this->em->remove($contact);
        $this->em->flush();

        //
        $this->flashMessage('Kontakt bol úspešne zmazaný', 'success');
        $this->redirect(':Contact:List:default');
    }

    /********************************************************************************
     * //                               Components
     ********************************************************************************/

    /**
     * Invoice datagrid
     *
     * @param string $name
     *
     * @return DataGrid
     */
    protected function createComponentContactGrid(string $name)
    {
        /** @var ContactRepository $repository */
        $repository = $this->em->getRepository(Contact::class);

        $data = $repository
            ->createQueryBuilder('contact')
            ->leftJoin(
                '\App\Entity\Address',
                'billingAddress',
                Join::WITH,
                'contact.billingAddress = billingAddress.id'
            )
            ->leftJoin(
                '\App\Entity\Address',
                'shippingAddress',
                Join::WITH,
                'contact.shippingAddress = shippingAddress.id'
            )
            ->innerJoin('\App\Entity\User', 'user', Join::WITH, 'contact.user = user.id');

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

        // Actions
        $grid->addAction('edit', 'Upraviť', ':Contact:Edit:', ['id' => 'id'])
             ->setRenderer(function (Contact $item) {
                 $link = $this->link(':Contact:Edit:default', ['id' => $item->getId()]);

                 //
                 return sprintf(
                     '
                    <a href="%s" class="btn btn-edit btn-sm">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="svg-icon--material svg-icon btn-icon" data-name="Material--Edit">
                            <path d="M0 0h24v24H0V0z" fill="none"></path>
                            <path d="M5 18.08V19h.92l9.06-9.06-.92-.92z" opacity="0.3"></path>
                            <path d="M20.71 7.04a.996.996 0 000-1.41l-2.34-2.34c-.2-.2-.45-.29-.71-.29s-.51.1-.7.29l-1.83 1.83 3.75 3.75 1.83-1.83zM3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM5.92 19H5v-.92l9.06-9.06.92.92L5.92 19z"></path>
                        </svg>
                    </a>',
                     $link
                 );
             })
             ->setIcon('pencil')
             ->setClass('btn btn-warning btn-sm');
        $grid->addAction('delete', 'Zmazať', ':Contact:List:delete', ['id' => 'id'])
             ->setRenderer(function (Contact $item) {
                 $link = $this->link(':Contact:List:delete', ['id' => $item->getId()]);

                 //
                 return sprintf(
                     '
                    <a href="%s" class="btn btn-delete btn-sm">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="svg-icon--material svg-icon btn-icon" data-name="Material--Delete">
                            <path d="M0 0h24v24H0V0z" fill="none"></path>
                            <path d="M8 9h8v10H8z" opacity="0.3"></path>
                            <path d="M15.5 4l-1-1h-5l-1 1H5v2h14V4zM6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM8 9h8v10H8V9z"></path>
                        </svg>
                    </a>',
                     $link
                 );
             })
             ->setIcon('trash')
             ->setClass('btn btn-danger btn-sm');

        $grid->setOuterFilterRendering(true);
        //set translator
        $grid->setTranslator($this->translator);


        return $grid;
    }
}