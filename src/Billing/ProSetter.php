<?php

namespace App\Billing;

use App\Entity\StatisticBuys;
use App\Entity\User;
use App\Notifications\Notificator;
use Doctrine\ORM\EntityManagerInterface;

class ProSetter
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Notificator
     */
    private $notificator;

    public function __construct(
        EntityManagerInterface $entityManager,
        Notificator $notificator
    ) {
        $this->entityManager = $entityManager;
        $this->notificator = $notificator;
    }

    /**
     * @param User $user
     *
     * @throws \Exception
     */
    public function setUser(User $user)
    {
        $proUntil = $this->getUntilDate($user);
        $user->setProUntil($proUntil);
        $this->entityManager->persist($user);
        $this->setToStatistic($user, $proUntil);
        $this->entityManager->flush();
        $this->notificator->proUserSet($user);
    }

    /**
     * @param User $user
     * @return \DateTime|null
     * @throws \Exception
     */
    private function getUntilDate(User $user)
    {
        $userProDate = $user->getProUntil();
        if (null === $userProDate) {
            $userProDate =  new \DateTime();
        }

        $userProDate->add(new \DateInterval('P1M'));
        return clone $userProDate;
    }

    /**
     * @param User      $user
     * @param \DateTime $proUntil
     *
     * @throws \Exception
     */
    private function setToStatistic(User $user, \DateTime $proUntil)
    {
        $statistic = new StatisticBuys();
        $statistic->setUser($user);
        $statistic->setUntil($proUntil);
        $this->entityManager->persist($statistic);
    }
}
