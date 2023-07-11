<?php
declare(strict_types=1);

namespace App\SubscriptionModule\Presenters;

use App\Entity\Ecommerce\Subscription;
use App\Repository\Ecommerce\SubscriptionRepository;
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
     * Datagrid
     *
     * @param string $name
     *
     * @return DataGrid
     */
    protected function createComponentSubscriptionGrid(string $name)
    {
        /** @var SubscriptionRepository $repository */
        $repository = $this->em->getRepository(Subscription::class);

        $data = $repository
            ->createQueryBuilder('subscription')
            // Filter
            ->where('subscription.user = :user')
            ->setParameter('user', $this->getLoggedUser());

        $grid = new DataGrid($this, $name);
        $grid->setStrictSessionFilterValues();
        $grid->setRememberState(false);
        $grid->setColumnsHideable();
        //set grid data source
        $grid->setDataSource($data);
        $grid->setDefaultSort(['created' => 'DESC']);

        // Columns
        $grid->addColumnDateTime('startAt', 'Začiatok', 'startAt')
             ->setFilterDateRange();
        $grid->addColumnDateTime('endAt', 'Koniec', 'endAt')
             ->setFilterDateRange();
        $grid->addColumnText('type', 'Typ predplatného', 'type');
        $grid->addColumnDateTime('createdAt', 'Vytvorené', 'createdAt')
             ->setFilterDateRange();

        $grid->setOuterFilterRendering(true);
        //set translator
        $grid->setTranslator($this->translator);


        return $grid;
    }
}