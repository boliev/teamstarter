<?php

namespace App\Repository;

use App\Entity\UserSpecializations;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param array|null $ids
     *
     * @return \Doctrine\ORM\Query
     */
    public function getAvailableSpecialists()
    {
        return $this->createQueryBuilder('u')
            ->innerJoin(UserSpecializations::class, 'us', 'WITH', 'us.user = u.id')
            ->where('u.enabled = true')
            ->orderBy('u.updatedAt', 'desc')
            ->getQuery();
    }
}
