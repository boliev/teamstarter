<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Project;
use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\Query
     */
    public function getPublishedQuery()
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.openVacancies', 'ov')
            ->where('p.progressStatus = :publishedStatus')
            ->andWhere('ov.vacant = true')
            ->setParameter('publishedStatus', Project::STATUS_PUBLISHED)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery();
    }
}
