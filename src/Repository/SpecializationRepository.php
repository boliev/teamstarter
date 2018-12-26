<?php

namespace App\Repository;

use App\Entity\Specialization;
use Doctrine\ORM\EntityRepository;

class SpecializationRepository extends EntityRepository
{
    /**
     * @return Specialization[]
     */
    public function getListForSelect()
    {
        $specializations = $this->findBy([], ['title' => 'ASC']);
        $list = [];
        $other = null;
        foreach ($specializations as $specialization) {
            /** @var Specialization $specialization */
            if (Specialization::ID_OTHER === $specialization->getId()) {
                $other = $specialization;
                continue;
            }
            $list[] = $specialization;
        }
        if (null !== $other) {
            $list[] = $other;
        }

        return $list;
    }
}
