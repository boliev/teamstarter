<?php

namespace App\Repository;

use App\Entity\Achievement;
use Doctrine\ORM\EntityRepository;

class AchievementRepository extends EntityRepository
{
    public function getEnabled()
    {
        return $this->findBy(['enabled' => true]);
    }

    public function getByName(string $name): ?Achievement
    {
        return $this->findOneBy(['name' => $name]);
    }
}
