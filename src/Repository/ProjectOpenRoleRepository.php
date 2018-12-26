<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ProjectOpenRoleRepository extends EntityRepository
{
    public function getVacantForProjectBuilder(Project $project): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.project = :project')
            ->andWhere('p.vacant = true')
            ->orderBy('p.name', 'ASC')
            ->setParameter('project', $project);
    }
}
