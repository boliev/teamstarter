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
        $query = $this->getPublished()
            ->orderBy('a.createdAt', 'DESC');

        return $query->getQuery();
    }

    private function getPublished()
    {
        return $this->createQueryBuilder('a')
            ->where('a.status = :publishedStatus')
            ->setParameter('publishedStatus', Article::STATUS_PUBLISHED);
    }
}
