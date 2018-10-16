<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Offer;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class OfferRepository extends EntityRepository
{
    /**
     * @param User    $user
     * @param Project $project
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return Offer|null
     */
    public function getUserOfferForProject(User $user, Project $project): ?Offer
    {
        return $this->createQueryBuilder('o')
            ->where('o.project = :project')
            ->andWhere('o.from = :user')
            ->andWhere('o.status in (:activeStatuses)')
            ->setParameter('project', $project)
            ->setParameter('user', $user)
            ->setParameter('activeStatuses', Offer::ACTIVE_STATUSES)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
