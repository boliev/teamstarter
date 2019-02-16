<?php

namespace App\Repository;

use App\Entity\PromoCode;
use Doctrine\ORM\EntityRepository;

class PromoCodeRepository extends EntityRepository
{
    /**
     * @param string $code
     *
     * @return PromoCode|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByCode(string $code): ?PromoCode
    {
        return $this->createQueryBuilder('p')
            ->where('p.code = :code')
            ->andWhere('p.until > :date')
            ->setParameter('code', $code)
            ->setParameter('date', new \DateTime())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
