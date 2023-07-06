<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\TaxDocument;
use Nette\Application\UI\ITemplateFactory;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

class EmailService
{
    /** @var ITemplateFactory @inject */
    public $templateFactory;

    /** @var TaxDocumentService @inject */
    public $taxDocumentService;

    /**
     * @param TaxDocument $taxDocument
     *
     * @throws \Exception
     * @return void
     */
    public function sendTaxDocument(TaxDocument $taxDocument): void
    {
        $pdfData = $this->taxDocumentService->generatePDF($taxDocument);
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
    }
}