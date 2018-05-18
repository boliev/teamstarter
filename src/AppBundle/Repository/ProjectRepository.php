<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Project;
use AppBundle\Entity\Specialization;
use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository
{
    /**
     * @return Specialization[]
     */
    public function getPublished()
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.openVacancies', 'ov')
            ->where('p.progressStatus = :publishedStatus')
            ->andWhere('ov.vacant = true')
            ->setParameter('publishedStatus', Project::STATUS_PUBLISHED)
            ->getQuery()
            ->getResult();
    }
}
