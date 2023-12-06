<?php

namespace App\Repository;

use App\Entity\TaxDocument;
use App\Entity\UserCompany;
use App\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaxDocument>
 *
 * @method TaxDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaxDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaxDocument[]    findAll()
 * @method TaxDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxDocumentRepository extends ServiceEntityRepository
{
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(TaxDocument $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(TaxDocument $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return TaxDocument|null
     */
    public function getUserCompanyLastTaxDocument(?UserCompany $userCompany)
    {
        $qb = $this->createQueryBuilder('td');

        $td = $qb->select('td')
                 ->where('td.userCompany = :userCompany')
                 ->setParameter('userCompany', $userCompany)
                 ->setMaxResults(1)
                 ->orderBy('td.createdAt', 'DESC')
                 ->getQuery()
                 ->getResult();

        if(isset($td[0])) {
            return $td[0];
        } else {
            return null;
        }
    }
}
