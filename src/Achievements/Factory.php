<?php

namespace App\Achievements;

use App\Entity;
use App\Repository\AchievementRepository;
use App\Repository\CommentRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;

class Factory
{
    /** @var AchievementRepository */
    private $achievementRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var CommentRepository */
    private $commentRepository;

    /** @var ProjectRepository */
    private $projectRepository;

    public function __construct(
        AchievementRepository $achievementRepository,
        EntityManagerInterface $entityManager,
        CommentRepository $commentRepository,
        ProjectRepository $projectRepository
    ) {
        $this->achievementRepository = $achievementRepository;
        $this->entityManager = $entityManager;
        $this->commentRepository = $commentRepository;
        $this->projectRepository = $projectRepository;
    }

    /**
     * @return AchievementInterface[]
     */
    public function getAchievements(): array
    {
        $achievements = [];
        $entities = $this->achievementRepository->getEnabled();

        foreach ($entities as $entity) {
            $achievements[] = $this->getAchievementByEntity($entity);
        }

        return $achievements;
    }

    private function getAchievementByEntity(Entity\Achievement $entity): AchievementInterface
    {
        switch ($entity->getName()) {
            case Entity\Achievement::JUNIOR_COMMENTS:
                return new JuniorComments($entity, $this->entityManager, $this->achievementRepository, $this->commentRepository);
                break;
            case Entity\Achievement::MIDDLE_COMMENTS:
                return new MiddleComments($entity, $this->entityManager, $this->achievementRepository, $this->commentRepository);
                break;
            case Entity\Achievement::SENIOR_COMMENTS:
                return new SeniorComments($entity, $this->entityManager, $this->achievementRepository, $this->commentRepository);
                break;
            case Entity\Achievement::ENTREPRENEUR:
                return new Entrepreneur($entity, $this->entityManager, $this->achievementRepository, $this->projectRepository);
                break;
            case Entity\Achievement::SERIAL_ENTREPRENEUR:
                return new SerialEntrepreneur($entity, $this->entityManager, $this->achievementRepository, $this->projectRepository);
                break;
            case Entity\Achievement::PROACTIVE:
                return new Proactive($entity, $this->entityManager, $this->achievementRepository, $this->commentRepository);
                break;
        }

        throw new Exception\AchievementClassNotFoundException();
    }
}
