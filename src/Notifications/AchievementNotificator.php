<?php

namespace App\Notifications;

use App\Entity\Achievement;
use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AchievementNotificator extends Notificator
{
    public function newAchievement(User $user, Achievement $achievement)
    {
        $this->sendEmail(
            [$user->getEmail()],
            $this->trans('achievements.new_achievement_email.subject'),
            $this->trans('achievements.new_achievement_email.message', [
                '%username%' => $this->getUsername($user),
                '%profile_link%' => $this->getSpecialistLink($user),
                '%title%' => $this->getAchievementName($achievement),
                '%achievements_link%' => $this->router->generate(
                    'achievements_page',
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            ])
        );

        $this->telegramSender->sendMessage(
            $this->foundersChatTg,
            $this->trans('achievements.new_achievement_admin_tg', [
                '%username%' => $this->getUsername($user),
                '%title%' => $this->getAchievementName($achievement),
            ])
        );
    }

    private function getAchievementName(Achievement $achievement): string
    {
        return $this->translator->trans("achievements.{$achievement->getName()}");
    }
}
