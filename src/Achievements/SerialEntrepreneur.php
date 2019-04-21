<?php

namespace App\Achievements;

use App\Entity\Achievement;
use App\Entity\User;
use App\Repository\AchievementRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;

class SerialEntrepreneur extends AchievementAbstract
{
    const COUNT_NEEDED = 3;
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

    public function isNeeded(User $user): bool
    {
        if ($this->alreadyApplied($this->achievementEntity, $user)) {
            return false;
        }

        $projectCount = $this->projectRepository->getPublishedCount($user);
        if ($projectCount >= self::COUNT_NEEDED) {
            return true;
        }

        return false;
    }

    public function apply(User $user): bool
    {
        $entrepreneurAchievement = $this->achievementRepository->getByName(Achievement::ENTREPRENEUR);
        if ($this->alreadyApplied($entrepreneurAchievement, $user)) {
            $this->removeAchievement($entrepreneurAchievement, $user);
        }

        parent::apply($user);

        return true;
    }
}
