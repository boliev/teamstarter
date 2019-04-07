<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Project;
use App\Entity\User;
use App\Entity\UserSubscriptions;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class UserSubscriptionsRepository extends EntityRepository
{
    /**
     * @param User    $user
     * @param Project $project
     *
     * @return UserSubscriptions|null
     *
     * @throws NonUniqueResultException
     */
    public function getUserSubscribedToProjectComments(User $user, Project $project): ?UserSubscriptions
    {
        return $this->getUserSubscribedToEntityComments($user, UserSubscriptions::EVENT_NEW_COMMENT_TO_PROJECT_ADDED, $project->getId());
    }

    /**
     * @param User    $user
     * @param Article $article
     *
     * @return UserSubscriptions|null
     *
     * @throws NonUniqueResultException
     */
    public function getUserSubscribedToArticleComments(User $user, Article $article): ?UserSubscriptions
    {
        return $this->getUserSubscribedToEntityComments($user, UserSubscriptions::EVENT_NEW_COMMENT_TO_ARTICLE_ADDED, $article->getId());
    }

    /**
     * @param User $user
     * @return bool
     * @throws NonUniqueResultException
     */
    public function isUserSubscribedToDigest(User $user): bool
    {
        return (bool) $this->createQueryBuilder('us')
        ->where('us.event = :event')
        ->andWhere('us.user = :user')
        ->setParameter('event', UserSubscriptions::DIGEST)
        ->setParameter('user', $user)
        ->getQuery()
        ->getOneOrNullResult();

    }

    /**
     * @param User   $user
     * @param string $event
     * @param int    $entityId
     *
     * @return mixed
     *
     * @throws NonUniqueResultException
     */
    private function getUserSubscribedToEntityComments(User $user, string $event, int $entityId)
    {
        return $this->createQueryBuilder('us')
            ->where('us.event = :event')
            ->andWhere('us.user = :user')
            ->andWhere('us.entityId = :entityId')
            ->setParameter('event', $event)
            ->setParameter('user', $user)
            ->setParameter('entityId', $entityId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
