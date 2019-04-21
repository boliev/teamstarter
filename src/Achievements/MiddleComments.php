<?php

namespace App\Achievements;

use App\Entity\Achievement;
use App\Entity\User;
use Doctrine\ORM\NonUniqueResultException;

class MiddleComments extends CommentsAchievementAbstract
{
    const COUNT_NEEDED = 50;

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

        $seniorComments = $this->achievementRepository->getByName(Achievement::SENIOR_COMMENTS);
        if ($this->alreadyApplied($seniorComments, $user)) {
            return false;
        }

        $commentsCount = $this->commentRepository->getCountForUser($user);
        if ($commentsCount >= self::COUNT_NEEDED && $commentsCount < SeniorComments::COUNT_NEEDED) {
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

        parent::apply($user);

        return true;
    }
}
