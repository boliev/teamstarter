<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Project;
use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
    public function getForProject(Project $project)
    {
        return $this->createQueryBuilder('c')
            ->where('c.entity = :entity')
            ->andWhere('c.toId = :id')
            ->andWhere('c.removed = false')
            ->orderBy('c.createdAt')
            ->setParameter('entity', Comment::ENTITY_PROJECT)
            ->setParameter('id', $project->getId())
            ->getQuery()
            ->getResult();
    }
}
