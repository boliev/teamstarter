<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use App\Entity\UserSubscriptions;
use Doctrine\ORM\EntityRepository;

class UserSubscriptionsRepository extends EntityRepository
{
    public function getUserSubscribedToProjectComments(User $user, Project $project): ?UserSubscriptions
    {
        return $this->createQueryBuilder('us')
            ->where('us.event = :event')
            ->andWhere('us.user = :user')
            ->andWhere('us.entityId = :projectId')
            ->setParameter('event', UserSubscriptions::EVENT_NEW_COMMENT_TO_POST_ADDED)
            ->setParameter('user', $user)
            ->setParameter('projectId', $project->getId())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
