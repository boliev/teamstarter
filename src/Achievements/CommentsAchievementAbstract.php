<?php

namespace App\Achievements;

use App\Entity\Achievement;
use App\Repository\AchievementRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;

abstract class CommentsAchievementAbstract extends AchievementAbstract
{
    /** @var CommentRepository */
    protected $commentRepository;

    public function __construct(
        Achievement $achievementEntity,
        EntityManagerInterface $entityManager,
        AchievementRepository $achievementRepository,
        CommentRepository $commentRepository
    ) {
        $this->commentRepository = $commentRepository;
        parent::__construct($achievementEntity, $entityManager, $achievementRepository);
    }
}
