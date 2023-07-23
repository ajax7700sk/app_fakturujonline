<?php
declare(strict_types=1);

namespace App\TaxDocumentModule\Presenters;

use App\Entity\DeliveryNote;
use App\Entity\TaxDocument;
use App\Entity\User;
use App\Entity\UserCompany;
use App\Repository\ContactRepository;
use App\Repository\DeliveryNoteRepository;
use App\Repository\TaxDocumentRepository;
use App\Service\EmailService;
use App\Service\FileService;
use App\Service\TaxDocumentService;
use App\TaxDocumentModule\Forms\ITaxDocumentPaymentForm;
use App\TaxDocumentModule\Forms\TaxDocumentPaymentForm;
use Doctrine\ORM\Query\Expr\Join;
use Dompdf\Dompdf;
use Dompdf\Options;
use Nette\Application\Responses\FileResponse;
use Nette\Mail\Message;
use Nette\Mail\SendException;
use Nette\Mail\SendmailMailer;
use Ublaboo\DataGrid\DataGrid;

class ListPresenter extends BasePresenter
{
    /** @var TaxDocumentService @inject */
    public $taxDocumentService;

    /** @var ITaxDocumentPaymentForm @inject */
    public $taxDocumentPaymentForm;

    /** @var EmailService @inject */
    public $emailService;

    /** @var UserCompany|null */
    private $userCompany;

    public function actionDefault()
    {
        $user = $this->getLoggedUser();
        /** @var UserCompany[] $userCompanies */
        $userCompanies = $this->em
            ->getRepository(UserCompany::class)
            ->findBy([
                'user' => $user,
            ]);
        //
        $this->template->userCompanies = $userCompanies;
        //

        foreach ($userCompanies as $userCompany) {
            $this->redirect(':TaxDocument:List:userCompany', ['id' => $userCompany->getId()]);
            break;
        }
    }

    public function actionUserCompany($id)
    {
        /** @var UserCompany|null $userCompany */
        $userCompany = $this->em->getRepository(UserCompany::class)->find((int) $id);
        $user = $this->getLoggedUser();

        if(!$userCompany) {
            $this->error();
        }
        $userCompanies = $this->em
            ->getRepository(UserCompany::class)
            ->findBy([
                'user' => $user,
            ]);


        $this->userCompany = $userCompany;
        //
        $this->template->userCompanies = $userCompanies;
        $this->template->userCompany = $userCompany;
    }

    public function actionDelete($id)
    {
        /** @var TaxDocument|null $taxDocument */
        $taxDocument = $this->em->getRepository(TaxDocument::class)->find((int) $id);

        if(!$taxDocument) {
            $this->error();
        }

        $this->em->remove($taxDocument);
        $this->em->flush();
        //
        $this->flashMessage('Doklad bol úspešne zmazaný', 'success');
        $this->redirect(':TaxDocument:List:default');
    }

    public function actionPdf($id)
    {
        /** @var TaxDocument|null $taxDocument */
        $taxDocument = $this->em->getRepository(TaxDocument::class)->find((int)$id);

        if ( ! $taxDocument) {
            $this->error();
        }

        //create template
        $data = $this->taxDocumentService->generatePDF($taxDocument);
        // Response
        //Send file response
        $fileResponse = new FileResponse(
            $data['filepath'],
            $data['filename'],
            'application/pdf',
            false
        );

        $this->sendResponse($fileResponse);
        //
        $this->terminate();
    }

    public function actionEmail($id)
    {
        if ( ! $this >> $this->hasActiveSubscription()) {
            $this->error();
        }

        /** @var TaxDocument|null $taxDocument */
        $taxDocument = $this->em->getRepository(TaxDocument::class)->find((int)$id);

        if ( ! $taxDocument) {
            $this->error();
        }

        try {
            $this->emailService->sendTaxDocument($taxDocument);
            //
            $this->flashMessage('E-mail bol úspešne odoslaný', 'success');
        } catch (SendException) {
            $this->flashMessage('Pri odoslaní e-mailu nastala chyba.', 'danger');
        } catch (\InvalidArgumentException $e) {
            $this->flashMessage($e->getMessage(), 'danger');
        }

        $this->redirect(':TaxDocument:List:default');
    }

    public function actionExport()
    {
        if ( ! $this >> $this->hasActiveSubscription()) {
            $this->error();
        }

        $ids = $this->request->getPost('id');
        //
        /** @var TaxDocumentRepository $repository */
        $repository = $this->em->getRepository(TaxDocument::class);
        $taxDocuments = $repository->findBy([
            'id' => $ids
        ]);

        $files = [];

        // Check if user is owner of document

        // ---
        foreach ($taxDocuments as $key => $taxDocument) {
            // Is user owner?
            if ($taxDocument->getUserCompany()->getUser()->getId() != $this->getLoggedUser()->getId()) {
                unset($taxDocuments[$key]);
            }

        }

        if(count($taxDocuments) == 0) {
            $this->flashMessage('Nebolo možné vyexportovať žiaden doklad', 'danger');
            $this->redirect(':TaxDocument:List:');
        }

        foreach ($taxDocuments as $taxDocument) {
            $data = $this->taxDocumentService->generatePDF($taxDocument);
            //
            $files[$data['filename']] = $data['filepath'];
        }

        // Export
        $data = $this->taxDocumentService->exportPdf($files);

        // --- Send file response
        $fileResponse = new FileResponse(
            $data['filepath'],
            $data['filename'],
        );

        $this->sendResponse($fileResponse);
        //
        $this->terminate();
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
    protected function createComponentTaxDocumentGrid(string $name)
    {
        /** @var ContactRepository $repository */
        $repository = $this->em->getRepository(TaxDocument::class);

        $data = $repository
            ->createQueryBuilder('taxDocument')
            ->leftJoin('\App\Entity\Contact', 'contact', Join::WITH, 'taxDocument.contact = contact.id')
            ->leftJoin(
                '\App\Entity\UserCompany',
                'userCompany',
                Join::WITH,
                'taxDocument.userCompany = userCompany.id'
            )
            // Filter
            ->andWhere('userCompany.user = :user')
            ->setParameter('user', $this->getLoggedUser());

        if($this->userCompany) {
            $data
                ->andWhere('userCompany = :userCompany')
                ->setParameter('userCompany', $this->userCompany);
        }

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
        $grid->addColumnDateTime('deliveryDateAt', 'Dátum dodania', 'deliveryDateAt')
             ->setFilterDateRange();
        $grid->addColumnDateTime('dueDateAt', 'Splatnosť', 'dueDateAt')
             ->setFilterDateRange();
        $grid->addColumnDateTime('createdAt', 'Vytvorené', 'createdAt')
             ->setFilterDateRange();
        $grid->addColumnDateTime('updatedAt', 'Upravené', 'updatedAt')
             ->setFilterDateRange();

        // Actions

        if($this->hasActiveSubscription()) {
            $grid->addAction('paidAt', '€', null)
                 ->setRenderer(function ($entity) {
                     return "<a target='_blank' 
                    title='Uhradené' 
                    data-target='#paymentModal' 
                    data-id='".$entity->getId()."' 
                    class='btn btn-primary btn-sm js-modal'>€</a>";
                 })
                 ->setClass('btn btn-info btn-sm btn-payment');
            $grid->addAction('pdf', 'PDF', ':TaxDocument:List:pdf', ['id' => 'id'])
                 ->setClass('btn btn-info btn-sm btn-pdf');
            $grid->addAction('email', 'E-mail', ':TaxDocument:List:email', ['id' => 'id'])
                  ->setRenderer(function(TaxDocument $item) {
                        $link = $this->link(':TaxDocument:List:email', ['id' => $item->getId()]);

                        // Check if supplier and subscriber has filled email
                      if (
                          ! $item->getSubscriberBillingAddress()->getEmail() ||
                          ! $item->getSupplierBillingAddress()->getEmail()
                      ) {
                          return '';
                      }

                        //
                        return sprintf('
                            <a href="%s" class="btn btn-email btn-sm">
                                <svg viewBox="0 0 24 24" fill="currentColor" class="svg-icon--material svg-icon btn-icon" data-name="Material--Email">
                                    <path d="M0 0h24v24H0V0z" fill="none"></path>
                                    <path d="M20 8l-8 5-8-5v10h16zm0-2H4l8 4.99z" opacity="0.3"></path>
                                    <path d="M4 20h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2zM20 6l-8 4.99L4 6h16zM4 8l8 5 8-5v10H4V8z"></path>
                                </svg>
                            </a>', $link
                        );
                    })
                 ->setClass('btn btn-info btn-sm');
        }
        $grid->addAction('edit', 'Upraviť', ':TaxDocument:Edit:default', ['id' => 'id'])
             ->setRenderer(function(TaxDocument $item) {
                 $link = $this->link(':TaxDocument:Edit:default', ['id' => $item->getId()]);
                 //
                 return sprintf('
                    <a href="%s" class="btn btn-edit btn-sm">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="svg-icon--material svg-icon btn-icon" data-name="Material--Edit">
                            <path d="M0 0h24v24H0V0z" fill="none"></path>
                            <path d="M5 18.08V19h.92l9.06-9.06-.92-.92z" opacity="0.3"></path>
                            <path d="M20.71 7.04a.996.996 0 000-1.41l-2.34-2.34c-.2-.2-.45-.29-.71-.29s-.51.1-.7.29l-1.83 1.83 3.75 3.75 1.83-1.83zM3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM5.92 19H5v-.92l9.06-9.06.92.92L5.92 19z"></path>
                        </svg>
                    </a>', $link
                 );
             })
             ->setIcon('pencil')
             ->setClass('btn btn-warning btn-sm');
        $grid->addAction('delete', 'Zmazať', ':TaxDocument:List:delete', ['id' => 'id'])
             ->setRenderer(function(TaxDocument $item) {
                 $link = $this->link(':TaxDocument:List:delete', ['id' => $item->getId()]);
                 //
                 return sprintf('
                    <a href="%s" class="btn btn-delete btn-sm">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="svg-icon--material svg-icon btn-icon" data-name="Material--Delete">
                            <path d="M0 0h24v24H0V0z" fill="none"></path>
                            <path d="M8 9h8v10H8z" opacity="0.3"></path>
                            <path d="M15.5 4l-1-1h-5l-1 1H5v2h14V4zM6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM8 9h8v10H8V9z"></path>
                        </svg>
                    </a>', $link
                 );
             })
             ->setIcon('trash')
             ->setClass('btn btn-danger btn-sm');
        // Action
        $grid->addGroupAction('exportPdf');
        $grid->setOuterFilterRendering(true);
        //set translator
        $grid->setTranslator($this->translator);


        return $grid;
    }

    /**
     * @return TaxDocumentPaymentForm
     */
    public function createComponentTaxDocumentPaymentForm(): TaxDocumentPaymentForm
    {
        /** @var TaxDocumentPaymentForm $control */
        $control = $this->taxDocumentPaymentForm->create();

        return $control;
    }

}