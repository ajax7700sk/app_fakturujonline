<?php
declare(strict_types=1);

namespace App\TaxDocumentModule\Presenters;

use App\Entity\DeliveryNote;
use App\Entity\TaxDocument;
use App\Repository\ContactRepository;
use App\Repository\DeliveryNoteRepository;
use App\Repository\TaxDocumentRepository;
use App\Service\FileService;
use App\Service\TaxDocumentService;
use App\TaxDocumentModule\Forms\ITaxDocumentPaymentForm;
use App\TaxDocumentModule\Forms\TaxDocumentPaymentForm;
use Doctrine\ORM\Query\Expr\Join;
use Dompdf\Dompdf;
use Dompdf\Options;
use Nette\Application\Responses\FileResponse;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Ublaboo\DataGrid\DataGrid;

class ListPresenter extends BasePresenter
{
    /** @var TaxDocumentService @inject */
    public $taxDocumentService;

    /** @var ITaxDocumentPaymentForm @inject */
    public $taxDocumentPaymentForm;

    public function actionDefault()
    {

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
        /** @var TaxDocument|null $taxDocument */
        $taxDocument = $this->em->getRepository(TaxDocument::class)->find((int)$id);

        if ( ! $taxDocument) {
            $this->error();
        }

        $pdfData = $this->generatePDF($taxDocument);

        // Template
        $template = $this->templateFactory->createTemplate();
        // Variables
        $template->taxDocument = $taxDocument;
        //
        $template->setFile(get_app_folder_path().'/templates/email/tax-document.latte');

        // Message
        $message = new Message();
        $message
            ->setSubject('Doklad č. '.$taxDocument->getNumber())
            ->setHtmlBody((string)$template)
            ->setFrom($taxDocument->getSupplierBillingAddress()->getEmail())
            ->addTo($taxDocument->getSubscriberBillingAddress()->getEmail())
            ->addAttachment($pdfData['filepath']);

        $mailer = new SendmailMailer();
        $mailer->send($message);

        $this->flashMessage('E-mail bol úspešne odoslaný', 'success');
        $this->redirect(':TaxDocument:List:default');
    }

    public function actionExport()
    {
        $ids = $this->request->getPost('id');
        //
        /** @var TaxDocumentRepository $repository */
        $repository = $this->em->getRepository(TaxDocument::class);
        $taxDocuments = $repository->findBy([
            'id' => $ids
        ]);

        $files = [];

        // ---
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
            );

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
        $grid->addAction('paidAt', 'Uhradiť', null)
             ->setRenderer(function ($entity) {
                 return "<a target='_blank' 
                data-toggle='modal' 
                title='Uhradené' 
                data-target='#paymentModal' 
                data-id='".$entity->getId()."' 
                class='btn btn-primary btn-sm'>Uhradiť</a>";
             })
             ->setClass('btn btn-info btn-sm');
        $grid->addAction('pdf', 'PDF', ':TaxDocument:List:pdf', ['id' => 'id'])
             ->setClass('btn btn-info btn-sm');
        $grid->addAction('email', 'E-mail', ':TaxDocument:List:email', ['id' => 'id'])
             ->setClass('btn btn-info btn-sm');
        $grid->addAction('edit', 'Upraviť', ':TaxDocument:Edit:', ['id' => 'id'])
             ->setIcon('pencil')
             ->setClass('btn btn-warning btn-sm');
        $grid->addAction('delete', 'Zmazať', ':TaxDocument:List:delete', ['id' => 'id'])
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