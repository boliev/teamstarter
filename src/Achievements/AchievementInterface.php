<?php

namespace App\Achievements;

use App\Entity\User;

interface AchievementInterface
{
    public function isNeeded(User $user): bool;

    public function apply(User $user): bool;
}
