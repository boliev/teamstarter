<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Message;
use AppBundle\Entity\Offer;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;

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

    public function getUserOffersSortedByLastMessage(User $user): array
    {
        $db = $this->createQueryBuilder('o');

        $offers = $db->where($this->getDialogExpression($user, $db))
            ->getQuery()
            ->getResult();
        $sortedOffers = [];
        /** @var Offer $offer */
        foreach ($offers as $offer) {
            /** @var Message $lastMessage */
            $lastMessage = $offer->getMessages()->first();
            if (!$lastMessage) {
                continue;
            }

            $sortedOffers[$lastMessage->getCreatedAt()->getTimestamp()] = $offer;
        }

        krsort($sortedOffers);

        return $sortedOffers;
    }

    private function getDialogExpression(User $user, QueryBuilder $db): Orx
    {
        $userProjects = [];
        foreach ($user->getProjects() as $project) {
            /* @var Project $project */
            $userProjects[] = $project->getId();
        }

        if (count($userProjects) > 0) {
            return $db->expr()->orX(
                $db->expr()->eq('o.from', $user->getId()),
                $db->expr()->in('o.project', $userProjects),
                $db->expr()->eq('o.to', $user->getId())
            );
        } else {
            return $db->expr()->orX(
                $db->expr()->eq('o.from', $user->getId()),
                $db->expr()->eq('o.to', $user->getId())
            );
        }
    }
}
