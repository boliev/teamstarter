<?php

namespace App\Tests;

use App\Achievements\MiddleComments;
use App\Achievements\SeniorComments;
use App\Entity\Achievement;
use App\Entity\User;
use App\Notifications\AchievementNotificator;
use App\Repository\AchievementRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MiddleCommentsTest extends TestCase
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
        $this->user->method('removeAchievement')->willReturn(true);
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
    public function isNeeded_AlreadyAppliedSenior_False()
    {
        $this->achievementRepository->method('getByName')->willReturnCallback(function ($name) {
            $achievement = new Achievement();
            $achievement->setName($name);

            return $achievement;
        });

        $this->user->method('hasAchievement')->willReturnCallback(function ($achievement) {
            return Achievement::SENIOR_COMMENTS === $achievement->getName();
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
            [-1], [0], [1], [MiddleComments::COUNT_NEEDED - 1], [SeniorComments::COUNT_NEEDED],
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
            [MiddleComments::COUNT_NEEDED], [SeniorComments::COUNT_NEEDED - 1],
        ];
    }

    /**
     * @test
     */
    public function Apply_RemoveJunior_Called()
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
    public function Apply_RemoveJunior_NotCalled()
    {
        $this->achievementRepository->method('getByName')->willReturn(new Achievement());
        $this->user->method('hasAchievement')->willReturn(false);

        $achievement = $this->createAchievement();

        $this->user->expects($this->never())->method('removeAchievement');
        $achievement->apply($this->user);
    }

    private function createAchievement()
    {
        return $achievement = new MiddleComments(
            $this->achievementEntity,
            $this->entityManager,
            $this->achievementRepository,
            $this->notificator,
            $this->commentRepository
        );
    }
}
