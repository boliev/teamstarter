<?php

namespace App\Achievements;

use App\Entity\Achievement;
use App\Entity\User;
use App\Repository\AchievementRepository;
use Doctrine\ORM\EntityManagerInterface;

abstract class AchievementAbstract implements AchievementInterface
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var Achievement */
    protected $achievementEntity;

    /** @var AchievementRepository */
    protected $achievementRepository;

    public function __construct(
        Achievement $achievementEntity,
        EntityManagerInterface $entityManager,
        AchievementRepository $achievementRepository
    ) {
        $this->entityManager = $entityManager;
        $this->achievementEntity = $achievementEntity;
        $this->achievementRepository = $achievementRepository;
    }

    public function apply(User $user): bool
    {
        $user->addAchievement($this->achievementEntity);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }

    public function alreadyApplied(Achievement $achievement, User $user): bool
    {
        return $user->hasAchievement($achievement);
    }

    protected function removeAchievement(Achievement $achievement, User $user)
    {
        return $user->removeAchievement($achievement);
    }
}
