<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\TaxDocument;
use Dompdf\Dompdf;
use Dompdf\Options;
use Nette\Application\UI\ITemplateFactory;

class TaxDocumentService
{
    /** @var ITemplateFactory @inject */
    public $templateFactory;

    public function generatePDF(TaxDocument $taxDocument): array
    {
        //create template
        $template = $this->templateFactory->createTemplate();
        // Variables
        $template->taxDocument = $taxDocument;
        $template->localeCode = $taxDocument->getLocaleCode();
        $template->currencyCode = $taxDocument->getCurrencyCode();
        $template->logo = get_app_www_folder_path() . $taxDocument->getUserCompany()->getLogo();
        //
        $template->setFile(get_app_folder_path() . '/TaxDocumentModule/templates/List/pdf.latte');

        //options
        $options = new Options();
        $options->setIsPhpEnabled(true);
        $options->setIsFontSubsettingEnabled(true);
        $options->setIsRemoteEnabled(true);
        $options->set('isRemoteEnabled', true);

        //render pdf with Dompdf
        $dompdf = new Dompdf($options);
        $dompdf->getCanvas()->get_page_count();
//        $template = str_replace(['[PP]'], [$dompdf->getCanvas()->get_page_count()], $template);
        $dompdf->loadHtml($template, 'UTF-8');
        $dompdf->setPaper('A4');
        $dompdf->render();

        $stream = $dompdf->output();
        $filename = sprintf('doklad-%s.pdf', $taxDocument->getNumber());
        $filepath = get_app_root_folder_path() . '/data/pdf/' . sprintf('doklad-%s.pdf', $taxDocument->getId());

        //
        file_put_contents($filepath, $stream);

        return array(
            'dompdf' => $dompdf,
            'filename' => $filename,
            'filepath' => $filepath
        );
    }

    public function exportPdf(array $files, string $filename = 'export.zip'): array
    {
        // Remove file
        if(file_exists(get_app_root_folder_path() . '/data/zip/' . $filename)) {
            unlink(get_app_root_folder_path() . '/data/zip/' . $filename);
        }
        //
        $zip     = new \ZipArchive();
        $zipName = time().".zip"; // Zip name
        $filepath = get_app_root_folder_path() . '/data/zip/' . $filename;

        //
        $zip->open($filepath, \ZipArchive::CREATE);

        foreach ($files as $name => $path) {
            if (file_exists($path)) {
                $zip->addFromString($name, file_get_contents($path));
            } else {
                throw new \InvalidArgumentException('File with path "%s" does not exists.', $path);
            }
        }

        $zip->close();

        return array(
            'filename' => $filename,
            'filepath' => $filepath
        );
    }

}