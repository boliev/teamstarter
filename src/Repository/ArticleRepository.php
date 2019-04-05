<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy(['removed' => false]);
    }

    public function getPublishedQuery()
    {
        $query = $this->getPublishedQueryBuilder()
            ->orderBy('a.publishedAt', 'DESC');

        return $query->getQuery();
    }

    public function getPublished()
    {
        return $this->getPublishedQueryBuilder()
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    private function getPublishedQueryBuilder()
    {
        return $this->createQueryBuilder('a')
            ->where('a.status = :publishedStatus')
            ->setParameter('publishedStatus', Article::STATUS_PUBLISHED);
    }
}
