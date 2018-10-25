<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ProjectRepository extends EntityRepository
{
    /**
     * @param array|null $ids
     *
     * @return \Doctrine\ORM\Query
     */
    public function getPublishedQuery(?array $ids = null)
    {
        $query = $this->getPublished()
            ->orderBy('p.createdAt', 'DESC');

        if (null !== $ids) {
            $query = $query->andWhere('p.id in (:ids)')
                ->setParameter(':ids', $ids);
        }

        return $query->getQuery();
    }

    public function getPublishedForUserQuery(User $user): QueryBuilder
    {
        return $this->getPublished()
            ->andWhere('p.user = :user')
            ->setParameter(':user', $user)
            ->orderBy('p.name', 'ASC');
    }

    private function getPublished()
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.openRoles', 'ov')
            ->where('p.progressStatus = :publishedStatus')
            ->setParameter('publishedStatus', Project::STATUS_PUBLISHED);
    }
}
