<?php

namespace App\Achievements;

use App\Entity\User;

class SeniorComments extends CommentsAchievementAbstract
{
    const COUNT_NEEDED = 200;

    public function isNeeded(User $user): bool
    {
        return false;
    }
}
