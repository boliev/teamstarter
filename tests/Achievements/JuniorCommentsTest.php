<?php

namespace App\Tests;

use App\Achievements\JuniorComments;
use App\Achievements\MiddleComments;
use App\Entity\Achievement;
use App\Entity\User;
use App\Notifications\AchievementNotificator;
use App\Repository\AchievementRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class JuniorCommentsTest extends TestCase
{
    /** @var MockObject */
    private $achievementEntity;

    /** @var MockObject */
    private $entityManager;

    /** @var MockObject */
    private $achievementRepository;

    /** @var MockObject */
    private $commentRepository;

    /** @var MockObject */
    private $user;

    /** @var MockObject */
    private $notificator;

    protected function setUp(): void
    {
        $this->achievementEntity = $this->createMock(Achievement::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->achievementRepository = $this->createMock(AchievementRepository::class);
        $this->commentRepository = $this->createMock(CommentRepository::class);
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
     */
    public function isNeeded_AlreadyAppliedMiddle_False()
    {
        $this->achievementRepository->method('getByName')->willReturnCallback(function($name) {
            $achievement = new Achievement();
            $achievement->setName($name);
            return $achievement;
        });

            $this->user->method('hasAchievement')->willReturnCallback(function ($achievement) {
                return $achievement->getName() === Achievement::MIDDLE_COMMENTS;
            });

            $achievement = $this->createAchievement();

            $result = $achievement->isNeeded($this->user);

            $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function isNeeded_AlreadyAppliedSenior_False()
    {
        $this->achievementRepository->method('getByName')->willReturnCallback(function($name) {
            $achievement = new Achievement();
            $achievement->setName($name);
            return $achievement;
        });

        $this->user->method('hasAchievement')->willReturnCallback(function ($achievement) {
            return $achievement->getName() === Achievement::SENIOR_COMMENTS;
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
        $this->commentRepository->method('getCountForUser')->willReturn($count);

        $achievement = $this->createAchievement();

        $result = $achievement->isNeeded($this->user);

        $this->assertFalse($result);
    }

    public function isNeeded_WrongCommentsCount_False_Provider()
    {
        return [
            [-1],[0],[1],[JuniorComments::COUNT_NEEDED-1],[MiddleComments::COUNT_NEEDED]
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
        $this->commentRepository->method('getCountForUser')->willReturn($count);

        $achievement = $this->createAchievement();

        $result = $achievement->isNeeded($this->user);

        $this->assertTrue($result);
    }

    public function isNeeded_Success_Provider()
    {
        return [
            [JuniorComments::COUNT_NEEDED],[MiddleComments::COUNT_NEEDED-1]
        ];
    }


    private function createAchievement()
    {
        return $achievement = new JuniorComments(
            $this->achievementEntity,
            $this->entityManager,
            $this->achievementRepository,
            $this->notificator,
            $this->commentRepository
        );
    }
}
