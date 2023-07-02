<?php

namespace App\EntityListener;

use Nette\DI\Container;

/**
 * Class AbstractListener
 *
 * @package App\EntityListener
 */
abstract class AbstractListener
{
    /** @var \Nette\DI\Container @inject */
    public $container;

    /** @var \Doctrine\ORM\EntityManagerInterface @inject */
    public $em;

}
