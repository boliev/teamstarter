<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository
{
    /**
     * @param User $user
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return int
     */
    public function getNewMessagesCount(User $user): int
    {
        return $this->createQueryBuilder('m')
            ->select('count(m.id)')
            ->where('m.to = :user')
            ->andWhere('m.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', Message::STATUS_NEW)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
