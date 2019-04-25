<?php

namespace App\Achievements;

use App\Entity\Achievement;
use App\Entity\User;
use Doctrine\ORM\NonUniqueResultException;

class JuniorComments extends CommentsAchievementAbstract
{
    const COUNT_NEEDED = 5;

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

        $middleComments = $this->achievementRepository->getByName(Achievement::MIDDLE_COMMENTS);
        if ($this->alreadyApplied($middleComments, $user)) {
            return false;
        }

        $seniorComments = $this->achievementRepository->getByName(Achievement::SENIOR_COMMENTS);
        if ($this->alreadyApplied($seniorComments, $user)) {
            return false;
        }

        $commentsCount = $this->commentRepository->getCountForUser($user);
        if ($commentsCount >= self::COUNT_NEEDED && $commentsCount < MiddleComments::COUNT_NEEDED) {
            return true;
        }

        return false;
    }
}
