<?php

namespace App\Tests;

use App\Achievements\SerialEntrepreneur;
use App\Entity\Achievement;
use App\Entity\User;
use App\Notifications\AchievementNotificator;
use App\Repository\AchievementRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SerialEntrepreneurTest extends TestCase
{
    /** @var MockObject */
    private $achievementEntity;

    /** @var MockObject */
    private $entityManager;

    /** @var MockObject */
    private $achievementRepository;

    /** @var MockObject */
    private $projectRepository;

    /** @var MockObject */
    private $user;

    /** @var MockObject */
    private $notificator;

    protected function setUp(): void
    {
        $this->achievementEntity = $this->createMock(Achievement::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->achievementRepository = $this->createMock(AchievementRepository::class);
        $this->projectRepository = $this->createMock(ProjectRepository::class);
        $this->user = $this->createMock(User::class);
        $this->notificator = $this->createMock(AchievementNotificator::class);

    }

    /**
     * @test
     */
    public function isNeeded_AlreadyApplied_False()
    {
        $this->user->method('hasAchievement')->willReturn(true);
        $achievement = $this->createAchievement();

        $result = $achievement->isNeeded($this->user);

        $this->assertFalse($result);
    }

    /**
     * @test
     *
     * @dataProvider isNeeded_WrongCommentsCount_False_Provider
     */
    public function isNeeded_WrongCommentsCount_False($count)
    {
        $this->achievementRepository->method('getByName')->willReturn(new Achievement());
        $this->user->method('hasAchievement')->willReturn(false);
        $this->projectRepository->method('getPublishedCount')->willReturn($count);

        $achievement = $this->createAchievement();

        $result = $achievement->isNeeded($this->user);

        $this->assertFalse($result);
    }

    public function isNeeded_WrongCommentsCount_False_Provider()
    {
        return [
            [0], [SerialEntrepreneur::COUNT_NEEDED - 1],
        ];
    }

    /**
     * @test
     *
     * @dataProvider isNeeded_Success_Provider
     */
    public function isNeeded_Success($count)
    {
        $this->achievementRepository->method('getByName')->willReturn(new Achievement());
        $this->user->method('hasAchievement')->willReturn(false);
        $this->projectRepository->method('getPublishedCount')->willReturn($count);

        $achievement = $this->createAchievement();

        $result = $achievement->isNeeded($this->user);

        $this->assertTrue($result);
    }

    public function isNeeded_Success_Provider()
    {
        return [
            [SerialEntrepreneur::COUNT_NEEDED],
        ];
    }

    /**
     * @test
     */
    public function Apply_RemoveEntrepreneur_Called()
    {
        $this->achievementRepository->method('getByName')->willReturn(new Achievement());
        $this->user->method('hasAchievement')->willReturn(true);

        $achievement = $this->createAchievement();

        $this->user->expects($this->once())->method('removeAchievement');
        $achievement->apply($this->user);
    }

    /**
     * @test
     */
    public function Apply_RemoveEntrepreneur_NotCalled()
    {
        $this->achievementRepository->method('getByName')->willReturn(new Achievement());
        $this->user->method('hasAchievement')->willReturn(false);

        $achievement = $this->createAchievement();

        $this->user->expects($this->never())->method('removeAchievement');
        $achievement->apply($this->user);
    }

    private function createAchievement()
    {
        return $achievement = new SerialEntrepreneur(
            $this->achievementEntity,
            $this->entityManager,
            $this->achievementRepository,
            $this->notificator,
            $this->projectRepository
        );
    }
}
