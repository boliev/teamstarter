<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Project;
use App\Entity\User;
use App\Entity\UserSpecializations;
use App\Entity\UserSubscriptions;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Ramsey\Uuid\Uuid;

class UserRepository extends EntityRepository
{
    /**
     * @return Query
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

    public function getAllUserSubscribedToProjectComments(Project $project)
    {
        return $this->getAllUserSubscribedToEntityComments(UserSubscriptions::EVENT_NEW_COMMENT_TO_PROJECT_ADDED, $project->getId());
    }

    public function getAllUserSubscribedToArticleComments(Article $article)
    {
        return $this->getAllUserSubscribedToEntityComments(UserSubscriptions::EVENT_NEW_COMMENT_TO_ARTICLE_ADDED, $article->getId());
    }

    public function getUserByUnsubscribeHash(string $hash): ?User
    {
        if (!Uuid::isValid($hash)) {
            return null;
        }

        return $this->findOneBy(['unsubscribeHash' => $hash]);
    }

    private function getAllUserSubscribedToEntityComments(string $event, int $entityId)
    {
        return $this->createQueryBuilder('u')
            ->innerJoin(UserSubscriptions::class, 'us', 'WITH', 'us.user = u.id')
            ->where('us.event = :event')
            ->andWhere('us.entityId = :entityId')
            ->setParameter('event', $event)
            ->setParameter('entityId', $entityId)
            ->getQuery()
            ->getResult();
    }
}
