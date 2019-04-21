<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

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

    /**
     * @param User $user
     *
     * @return int
     *
     * @throws NonUniqueResultException
     */
    public function getCountForUser(User $user): int
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.from = :user')
            ->andWhere('c.removed = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param User $user
     *
     * @return int
     */
    public function getCommentedProjectsCount(User $user): int
    {
        $result = $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.from = :user')
            ->andWhere('c.entity = :entity')
            ->andWhere('c.removed = false')
            ->addGroupBy('c.from')
            ->addGroupBy('c.toId')
            ->setParameter('user', $user)
            ->setParameter('entity', Comment::ENTITY_PROJECT)
            ->getQuery()
            ->getResult();

        return count($result);
    }
}
