<?php

namespace App\Achievements;

use App\Entity\User;

class MiddleComments extends CommentsAchievementAbstract
{
    const COUNT_NEEDED = 50;

    public function isNeeded(User $user): bool
    {
        return false;
    }
}
