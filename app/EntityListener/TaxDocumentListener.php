<?php
declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\LineItem;
use App\Entity\TaxDocument;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Mapping as ORM;

class TaxDocumentListener extends AbstractListener
{
    /**
     * @ORM\PreFlush()
     * @param TaxDocument $taxDocument
     * @param PreFlushEventArgs $args
     */
    public function preFlush(TaxDocument $taxDocument, PreFlushEventArgs $args)
    {
        // Recalculate price
        $taxDocument->recalculateTotals();
    }

    // ---------------------------------------- Helpers --------------------------------------------- \\
}