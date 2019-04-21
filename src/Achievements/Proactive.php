<?php

namespace App\Achievements;

use App\Entity\User;

class Proactive extends AchievementAbstract
{
    public function isNeeded(User $user): bool
    {
        return false;
    }
}
