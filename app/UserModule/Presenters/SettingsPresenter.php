<?php
declare(strict_types=1);

namespace App\UserModule\Presenters;

use App\Entity\UserCompany;
use App\Repository\ContactRepository;
use App\UserModule\Forms\IUserSettingsForm;
use App\UserModule\Forms\UserSettingsForm;
use Doctrine\ORM\Query\Expr\Join;
use Ublaboo\DataGrid\DataGrid;

class SettingsPresenter extends BasePresenter
{
    /** @var IUserSettingsForm @inject */
    public $userSettingsForm;

    public function actionDefault()
    {
        //
    }

    /*********************************************************************
     * Components
     ********************************************************************/

    public function createComponentUserSettingsForm(): UserSettingsForm
    {
        //
        /** @var UserSettingsForm $control */
        $control = $this->userSettingsForm->create();
        $control->setUser($this->getLoggedUser());

        return $control;
    }

    /**
     * Invoice datagrid
     *
     * @param string $name
     *
     * @return DataGrid
     */
    protected function createComponentUserCompanyGrid(string $name)
    {
        /** @var ContactRepository $repository */
        $repository = $this->em->getRepository(UserCompany::class);

        $data = $repository
            ->createQueryBuilder('userCompany')
            ->leftJoin(
                '\App\Entity\Address',
                'billingAddress',
                Join::WITH,
                'userCompany.billingAddress = billingAddress.id'
            )
            ->leftJoin(
                '\App\Entity\Address',
                'shippingAddress',
                Join::WITH,
                'userCompany.shippingAddress = shippingAddress.id'
            )
            ->innerJoin('\App\Entity\User', 'user', Join::WITH, 'userCompany.user = user.id');

        $grid = new DataGrid($this, $name);
        $grid->setStrictSessionFilterValues();
//        $grid->setRememberState(false);
        $grid->setColumnsHideable();
        //set grid data source
        $grid->setDataSource($data);
        $grid->setDefaultSort(['name' => 'ASC']);

        // Grid columns
        $grid->addColumnText('name', 'Názov', 'name')
             ->setFilterText();
        $grid->addColumnText('vatPayer', 'Plátca DPH', 'vatPayer')
             ->setFilterText();
        $grid->addColumnDateTime('createdAt', 'Vytvorené', 'createdAt')
             ->setFilterDateRange();

        $grid->setOuterFilterRendering(true);
        //set translator
        $grid->setTranslator($this->translator);


        return $grid;
    }
}