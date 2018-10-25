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

    /**
     * @param User $user
     * @param User $specialist
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return Offer|null
     */
    public function getUserOfferForSpecialist(User $user, User $specialist): ?Offer
    {
        return $this->createQueryBuilder('o')
            ->where('o.to = :specialist')
            ->andWhere('o.from = :user')
            ->andWhere('o.status in (:activeStatuses)')
            ->setParameter('specialist', $specialist)
            ->setParameter('user', $user)
            ->setParameter('activeStatuses', Offer::ACTIVE_STATUSES)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
