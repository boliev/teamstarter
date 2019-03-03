<?php

namespace App\Repository;

use App\Entity\UserSpecializations;
use Doctrine\Common\Collections\Collection;
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
            ->andWhere('u.lookingForProject = true')
            ->orderBy('u.updatedAt', 'desc')
            ->getQuery();
    }

    public function getAdminsEmails(): array
    {
        $emails = $this->createQueryBuilder('u')
            ->select('u.email')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%ROLE_ADMIN%')
            ->getQuery()
            ->getScalarResult();

        return array_column($emails, 'email');
    }
}
