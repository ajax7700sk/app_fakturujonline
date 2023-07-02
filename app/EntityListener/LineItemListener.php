<?php

namespace App\EntityListener;

use App\Entity\LineItem;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class ContactListener
 * @package App\EntityListener
 */
class LineItemListener extends AbstractListener
{

    /**
     * @ORM\PreFlush()
     * @param LineItem $lineItem
     * @param PreFlushEventArgs $args
     */
    public function preFlush(LineItem $lineItem, PreFlushEventArgs $args)
    {
        //
    }

    /**
     * @ORM\PrePersist()
     * @param LineItem $lineItem
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LineItem $lineItem, LifecycleEventArgs $event)
    {
        //
    }

    /**
     * @ORM\PreRemove()
     * @param LineItem $lineItem
     * @param LifecycleEventArgs $event
     */
    public function preRemove(LineItem $lineItem, LifecycleEventArgs $event)
    {
        //
    }

    // ---------------------------------------- Helpers --------------------------------------------- \\

}