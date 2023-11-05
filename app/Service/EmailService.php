<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\TaxDocument;
use App\Entity\User;
use Nette\Application\UI\ITemplateFactory;
use Nette\Mail\Message;
use Nette\Mail\SendException;
use Nette\Mail\SendmailMailer;
use Nette\Mail\SmtpMailer;
use Nette\Utils\Validators;

class EmailService
{
    /** @var ITemplateFactory @inject */
    public $templateFactory;

    /** @var TaxDocumentService @inject */
    public $taxDocumentService;

    /**
     * @param TaxDocument $taxDocument
     *
     * @return void
     * @throws SendException
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

        // Validate
        if ( ! Validators::isEmail($taxDocument->getSupplierBillingAddress()->getEmail())) {
            throw new \InvalidArgumentException('Email odosielateľa nie je platný');
        }

        if ( ! Validators::isEmail($taxDocument->getSubscriberBillingAddress()->getEmail())) {
            throw new \InvalidArgumentException('Email prijímateľa nie je platný');
        }

        // Message
        $message = new Message();
        $message
            ->setSubject('Doklad č. '.$taxDocument->getNumber())
            ->setHtmlBody((string)$template)
            ->setFrom('no-reply@fakturujonline.sk')
            ->addReplyTo($taxDocument->getSupplierBillingAddress()->getEmail())
            ->addTo($taxDocument->getSubscriberBillingAddress()->getEmail())
            ->addAttachment($pdfData['filepath']);

        $mailer = $this->getSMTPMailer();
        $mailer->send($message);
    }

    /**
     * @param TaxDocument $taxDocument
     *
     * @return void
     * @throws SendException
     */
    public function resetPassword(User $user, string $resetLink): void
    {
        // Template
        $template = $this->templateFactory->createTemplate();
        // Variables
        $template->user      = $user;
        $template->resetLink = $resetLink;
        //
        $template->setFile(get_app_folder_path().'/templates/email/reset-password.latte');

        // Message
        $message = new Message();
        $message
            ->setSubject('Obnova hesla')
            ->setHtmlBody((string)$template)
            ->setFrom('no-reply@fakturujonline.sk')
            ->addTo($user->getEmail());

        $mailer = $this->getSMTPMailer();
        $mailer->send($message);
    }

    /**
     * @return void
     * @throws SendException
     */
    public function registration(User $user): void
    {
        // Template
        $template = $this->templateFactory->createTemplate();
        // Variables
        $template->user = $user;
        //
        $template->setFile(get_app_folder_path().'/templates/email/registration.latte');

        // Message
        $message = new Message();
        $message
            ->setSubject('Vytvorenie účtu')
            ->setHtmlBody((string)$template)
            ->setFrom('no-reply@fakturujonline.sk')
            ->addTo($user->getEmail());

        $mailer = $this->getSMTPMailer();
        $mailer->send($message);
    }

    private function getBrevoSMTPMailer(): SmtpMailer
    {
        return new SmtpMailer(array(
            'host'     => 'smtp-relay.brevo.com',
            'username' => 'fakturujonline@gmail.com',
            'password' => '2fvUZ0dg8rNSQsq5',
            'port'     => 587,
        ));
    }

    private function getSMTPMailer(): SmtpMailer
    {
        return $this->getBrevoSMTPMailer();
    }
}