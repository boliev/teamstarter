<?php

namespace App\Tests;

use App\Billing\ProSetter;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProSetterTest extends TestCase
{
    /**
     * @test
     * @dataProvider setUserSuccessProvider
     *
     * @param string|null $proDate
     *
     * @throws \Exception
     */
    public function setUserSuccess(?string $proDate)
    {
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $mailer = $this->getMockBuilder(\Swift_Mailer::class)->disableOriginalConstructor()->getMock();
        $translator = $this->getMockBuilder(TranslatorInterface::class)->disableOriginalConstructor()->getMock();
        $setter = new ProSetter($entityManager, 'test@test.com', 'test@test.com', $mailer, 'test@test.com', $translator);
        $dateBefore = new \DateTime($proDate);
        $dateBefore->add(new \DateInterval('P1M'));
        $time = $dateBefore->getTimestamp();

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setProUntil(new \DateTime($proDate));

        $setter->setUser($user);

        $diff = $user->getProUntil()->getTimestamp() - $time;

        $this->assertLessThan(3, $diff);
    }

    public function setUserSuccessProvider()
    {
        return [
            [null],
            ['29.10.2018T22:00:00'],
            ['21.06.2018T22:00:00'],
            ['29.12.2018T22:00:00'],
            ['20.02.2019T22:00:00'],
        ];
    }
}
