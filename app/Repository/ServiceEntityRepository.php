<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008 Filip Procházka (filip@prochazka.su)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace App\Repository;

use Doctrine;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NoResultException;


/**
 * This class is an extension to EntityRepository and should help you with prototyping.
 * The first and only rule with EntityRepository is not to ever inherit them, ever.
 *
 * The only valid reason to inherit EntityRepository is to add more common methods to all EntityRepositories in application,
 * when you're creating your own framework (but do we really need to go any deeper than this?).
 *
 * @author Filip Procházka <filiap@prochazka.su>
 */
class ServiceEntityRepository extends Doctrine\ORM\EntityRepository//, Persistence\ObjectFactory
{
    //
}
