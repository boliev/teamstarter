<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy(['removed' => false]);
    }
}
