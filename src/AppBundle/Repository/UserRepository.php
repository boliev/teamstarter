<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param array|null $ids
     *
     * @return \Doctrine\ORM\Query
     */
    public function getAvailableSpecialists(?array $ids = null)
    {
        $query = $this->createQueryBuilder('u')
            ->innerJoin('u.userSpecializations', 'us')
            ->where('u.enabled = true')
            ->orderBy('u.createdAt', 'DESC');

        if (null !== $ids) {
            $query = $query->andWhere('u.id in (:ids)')
                ->setParameter(':ids', $ids);
        }

        return $query->getQuery();
    }
}
