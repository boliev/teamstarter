<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Project;
use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository
{
    /**
     * @param array|null $ids
     *
     * @return \Doctrine\ORM\Query
     */
    public function getPublishedQuery(?array $ids = null)
    {
        $query = $this->createQueryBuilder('p')
            ->innerJoin('p.openRoles', 'ov')
            ->where('p.progressStatus = :publishedStatus')
            ->setParameter('publishedStatus', Project::STATUS_PUBLISHED)
            ->orderBy('p.createdAt', 'DESC');

        if (null !== $ids) {
            $query = $query->andWhere('p.id in (:ids)')
                ->setParameter(':ids', $ids);
        }

        return $query->getQuery();
    }
}
