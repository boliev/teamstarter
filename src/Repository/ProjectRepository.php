<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

class ProjectRepository extends EntityRepository
{
    /**
     * @param User $user
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return int
     */
    public function getPublishedCount(User $user): int
    {
        return $this->getPublished()
            ->select('count(p.id)')
            ->andWhere('p.user = :user')
            ->setParameter(':user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

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

    public function getPublishedOrderedByIdQuery(string $order = 'DESC')
    {
        return $this->getPublished()
            ->orderBy('p.id', $order)
            ->setMaxResults(11)
            ->getQuery();
    }

    private function getPublished()
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.openRoles', 'ov')
            ->where('p.progressStatus = :publishedStatus')
            ->setParameter('publishedStatus', Project::STATUS_PUBLISHED);
    }
}
