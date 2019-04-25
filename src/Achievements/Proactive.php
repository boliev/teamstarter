<?php

namespace App\Achievements;

use App\Entity\User;

class Proactive extends CommentsAchievementAbstract
{
    const COUNT_NEEDED = 50;

    public function isNeeded(User $user): bool
    {
        if ($this->alreadyApplied($this->achievementEntity, $user)) {
            return false;
        }

        $commentedProjectCount = $this->commentRepository->getCommentedProjectsCount($user);
        if ($commentedProjectCount >= self::COUNT_NEEDED) {
            return true;
        }

        return false;
    }

    public function apply(User $user): bool
    {
        parent::apply($user);
        return true;
    }
}
