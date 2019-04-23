<?php

namespace App\Tests;

use App\Achievements\Entrepreneur;
use App\Achievements\SerialEntrepreneur;
use App\Entity\Achievement;
use App\Entity\User;
use App\Notifications\AchievementNotificator;
use App\Repository\AchievementRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EntrepreneurTest extends TestCase
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
        $this->notificator = $this->createMock(AchievementNotificator::class);
        $this->user = $this->createMock(User::class);
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
     */
    public function isNeeded_AlreadyAppliedSerial_False()
    {
        $this->achievementRepository->method('getByName')->willReturnCallback(function ($name) {
            $achievement = new Achievement();
            $achievement->setName($name);

            return $achievement;
        });

        $this->user->method('hasAchievement')->willReturnCallback(function ($achievement) {
            return Achievement::SERIAL_ENTREPRENEUR === $achievement->getName();
        });

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
            [Entrepreneur::COUNT_NEEDED - 1], [SerialEntrepreneur::COUNT_NEEDED],
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
            [Entrepreneur::COUNT_NEEDED], [SerialEntrepreneur::COUNT_NEEDED - 1],
        ];
    }

    private function createAchievement()
    {
        return $achievement = new Entrepreneur(
            $this->achievementEntity,
            $this->entityManager,
            $this->achievementRepository,
            $this->notificator,
            $this->projectRepository
        );
    }
}
