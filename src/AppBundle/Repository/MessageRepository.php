<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Message;
use AppBundle\Entity\Offer;
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
    public function getUserNewMessagesCount(User $user): int
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

    public function getOfferNewMessagesCount(Offer $offer, User $user): int
    {
        return $this->createQueryBuilder('m')
            ->select('count(m.id)')
            ->where('m.offer = :offer')
            ->andWhere('m.status = :status')
            ->andWhere('m.to = :user')
            ->setParameter('offer', $offer)
            ->setParameter('user', $user)
            ->setParameter('status', Message::STATUS_NEW)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getNewOfferMessages(Offer $offer, User $user)
    {
        return $offerNewMessages = $this->createQueryBuilder('m')
            ->where('m.offer = :offer')
            ->andWhere('m.status = :status')
            ->andWhere('m.to = :user')
            ->setParameter('offer', $offer)
            ->setParameter('user', $user)
            ->setParameter('status', Message::STATUS_NEW)
            ->getQuery()
            ->getResult();
    }
}
