<?php

namespace App\Achievements;

use App\Entity\Achievement;
use App\Entity\User;
use App\Repository\AchievementRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class Entrepreneur extends AchievementAbstract
{
    const COUNT_NEEDED = 1;

    /** @var ProjectRepository */
    private $projectRepository;

    public function __construct(
        Achievement $achievementEntity,
        EntityManagerInterface $entityManager,
        AchievementRepository $achievementRepository,
        ProjectRepository $projectRepository
    ) {
        $this->projectRepository = $projectRepository;
        parent::__construct($achievementEntity, $entityManager, $achievementRepository);
    }

    /**
     * @param User $user
     *
     * @return bool
     *
     * @throws NonUniqueResultException
     */
    public function isNeeded(User $user): bool
    {
        if ($this->alreadyApplied($this->achievementEntity, $user)) {
            return false;
        }

        $serialEntrepreneurAchievement = $this->achievementRepository->getByName(Achievement::SERIAL_ENTREPRENEUR);
        if ($this->alreadyApplied($serialEntrepreneurAchievement, $user)) {
            return false;
        }

        $projectCount = $this->projectRepository->getPublishedCount($user);
        if ($projectCount >= self::COUNT_NEEDED && $projectCount < SerialEntrepreneur::COUNT_NEEDED) {
            return true;
        }

        return false;
    }
}
