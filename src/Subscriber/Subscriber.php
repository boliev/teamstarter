<?php

namespace App\Subscriber;

use App\Entity\User;
use App\Entity\UserSubscriptions;
use App\Repository\UserSubscriptionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class Subscriber
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserSubscriptionsRepository
     */
    private $userSubscriptionsRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserSubscriptionsRepository $userSubscriptionsRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->userSubscriptionsRepository = $userSubscriptionsRepository;
    }

    /**
     * @param User $user
     * @return UserSubscriptions|null
     * @throws NonUniqueResultException
     */
    public function subscribeToDigest(User $user): ?UserSubscriptions
    {
        if(!$this->userSubscriptionsRepository->isUserSubscribedToDigest($user)) {
            $subscription = new UserSubscriptions();
            $subscription->setEvent(UserSubscriptions::DIGEST);
            $subscription->setUser($user);
            $this->entityManager->persist($subscription);
            $this->entityManager->flush();
            return $subscription;
        }

        return null;
    }
}