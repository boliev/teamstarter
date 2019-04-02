<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Project;
use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
    public function getForProject(Project $project)
    {
        return $this->getForEntity(Comment::ENTITY_PROJECT, $project->getId());
    }

    public function getForArticle(Article $article)
    {
        return $this->getForEntity(Comment::ENTITY_ARTICLE, $article->getId());
    }

    private function getForEntity(string $name, string $id)
    {
        return $this->createQueryBuilder('c')
            ->where('c.entity = :entity')
            ->andWhere('c.toId = :id')
            ->andWhere('c.removed = false')
            ->orderBy('c.createdAt')
            ->setParameter('entity', $name)
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
}
