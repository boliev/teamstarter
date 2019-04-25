<?php

namespace App\Achievements;

use App\Entity\Achievement;
use App\Entity\User;
use Doctrine\ORM\NonUniqueResultException;

class SeniorComments extends CommentsAchievementAbstract
{
    const COUNT_NEEDED = 200;

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

        $commentsCount = $this->commentRepository->getCountForUser($user);
        if ($commentsCount >= self::COUNT_NEEDED) {
            return true;
        }

        return false;
    }

    public function apply(User $user): bool
    {
        $juniorCommentsAchievement = $this->achievementRepository->getByName(Achievement::JUNIOR_COMMENTS);
        if ($this->alreadyApplied($juniorCommentsAchievement, $user)) {
            $this->removeAchievement($juniorCommentsAchievement, $user);
        }

        $middleCommentsAchievement = $this->achievementRepository->getByName(Achievement::MIDDLE_COMMENTS);
        if ($this->alreadyApplied($middleCommentsAchievement, $user)) {
            $this->removeAchievement($middleCommentsAchievement, $user);
        }

        parent::apply($user);

        return true;
    }
}
