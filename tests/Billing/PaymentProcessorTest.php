<?php

namespace App\Tests;

use App\Billing\Exception;
use App\Billing\PaymentFetcher;
use App\Billing\PaymentProcessor;
use App\Entity\Payment;
use App\Entity\User;
use App\Service\UserService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PaymentProcessorTest extends TestCase
{
    /**
     * @test
     * @dataProvider setProPaymentNoPaymentIdExceptionProvider
     * @param $data
     * @throws \Exception
     */
    public function setProPaymentNoPaymentIdException($data)
    {
        $paymentFetcher = $this->getMockBuilder(PaymentFetcher::class)->disableOriginalConstructor()->getMock();
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $userService = $this->getMockBuilder(UserService::class)->disableOriginalConstructor()->getMock();

        $processor = new PaymentProcessor($paymentFetcher, $entityManager, $userService);
        $this->expectException(Exception\NoPaymentIdException::class);
        $processor->setProPayment($data);
    }

    /**
     * @return array
     */
    public function setProPaymentNoPaymentIdExceptionProvider(): array
    {
        return [
            [[]],
            [null],
        ];
    }

    /**
     * @test
     * @dataProvider setProPaymentNoPaymentFoundExceptionProvider
     * @param $data
     * @param $payment
     * @throws \Exception
     */
    public function setProPaymentNoPaymentFoundException($data, $payment)
    {
        $paymentFetcher = $this->getMockBuilder(PaymentFetcher::class)->disableOriginalConstructor()->getMock();
        $repository = $this->getMockBuilder(ObjectRepository::class)->disableOriginalConstructor()->getMock();
        $repository->method('findOneBy')->willReturn($payment);
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $entityManager->method('getRepository')->willReturn($repository);
        $userService = $this->getMockBuilder(UserService::class)->disableOriginalConstructor()->getMock();

        $processor = new PaymentProcessor($paymentFetcher, $entityManager, $userService);
        $this->expectException(Exception\NoPaymentFoundException::class);
        $processor->setProPayment($data);
    }

    /**
     * @return array
     */
    public function setProPaymentNoPaymentFoundExceptionProvider(): array
    {
        return [
            [['object' => ['id' => 1]], false],
            [['object' => ['id' => 1]], null],
        ];
    }

    /**
     * @test
     * @dataProvider setProPaymentAlreadyPayedExceptionProvider
     * @param $data
     * @throws \Exception
     */
    public function setProPaymentAlreadyPayedException($data)
    {
        $payment = $this->getMockBuilder(Payment::class)->disableOriginalConstructor()->getMock();
        $payment->method('isPaid')->willReturn(true);
        $paymentFetcher = $this->getMockBuilder(PaymentFetcher::class)->disableOriginalConstructor()->getMock();
        $repository = $this->getMockBuilder(ObjectRepository::class)->disableOriginalConstructor()->getMock();
        $repository->method('findOneBy')->willReturn($payment);
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $entityManager->method('getRepository')->willReturn($repository);
        $userService = $this->getMockBuilder(UserService::class)->disableOriginalConstructor()->getMock();

        $processor = new PaymentProcessor($paymentFetcher, $entityManager, $userService);
        $this->expectException(Exception\PaymentAlreadyPayedException::class);
        $processor->setProPayment($data);
    }

    /**
     * @return array
     */
    public function setProPaymentAlreadyPayedExceptionProvider(): array
    {
        return [
            [['object' => ['id' => 1]]],
            [['object' => ['id' => 1]]],
        ];
    }

    /**
     * @test
     * @dataProvider setProPaymentPaymentNotFoundInYandexExceptionProvider
     * @param $data
     * @param $fromYandexPayment
     * @throws \Exception
     */
    public function setProPaymentPaymentNotFoundInYandexException($data, $fromYandexPayment)
    {
        $payment = $this->getMockBuilder(Payment::class)->disableOriginalConstructor()->getMock();
        $payment->method('isPaid')->willReturn(false);
        $paymentFetcher = $this->getMockBuilder(PaymentFetcher::class)->disableOriginalConstructor()->getMock();
        $paymentFetcher->method('fetch')->willReturn($fromYandexPayment);
        $repository = $this->getMockBuilder(ObjectRepository::class)->disableOriginalConstructor()->getMock();
        $repository->method('findOneBy')->willReturn($payment);
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $entityManager->method('getRepository')->willReturn($repository);
        $userService = $this->getMockBuilder(UserService::class)->disableOriginalConstructor()->getMock();

        $processor = new PaymentProcessor($paymentFetcher, $entityManager, $userService);
        $this->expectException(Exception\PaymentNotFoundInYandexException::class);
        $processor->setProPayment($data);
    }

    /**
     * @return array
     */
    public function setProPaymentPaymentNotFoundInYandexExceptionProvider(): array
    {
        return [
            [['object' => ['id' => 1]], null],
            [['object' => ['id' => 1]], false],
            [['object' => ['id' => 1]], []],
        ];
    }

    /**
     * @test
     * @dataProvider setProPaymentBadPaymentFromYandexExceptionProvider
     * @param $data
     * @param $fromYandexPayment
     * @throws \Exception
     */
    public function setProPaymentBadPaymentFromYandexException($data, $fromYandexPayment)
    {
        $payment = $this->getMockBuilder(Payment::class)->disableOriginalConstructor()->getMock();
        $payment->method('isPaid')->willReturn(false);
        $paymentFetcher = $this->getMockBuilder(PaymentFetcher::class)->disableOriginalConstructor()->getMock();
        $paymentFetcher->method('fetch')->willReturn($fromYandexPayment);
        $repository = $this->getMockBuilder(ObjectRepository::class)->disableOriginalConstructor()->getMock();
        $repository->method('findOneBy')->willReturn($payment);
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $entityManager->method('getRepository')->willReturn($repository);
        $userService = $this->getMockBuilder(UserService::class)->disableOriginalConstructor()->getMock();

        $processor = new PaymentProcessor($paymentFetcher, $entityManager, $userService);
        $this->expectException(Exception\BadPaymentFromYandexException::class);
        $processor->setProPayment($data);
    }

    /**
     * @return array
     */
    public function setProPaymentBadPaymentFromYandexExceptionProvider(): array
    {
        return [
            [['object' => ['id' => 1]], ['foo' => 'bar']],
            [['object' => ['id' => 1]], ['status' => '1']],
            [['object' => ['id' => 1]], ['paid' => true]],
        ];
    }

    /**
     * @test
     * @dataProvider setProPaymentReceivedButNotPayedExceptionProvider
     * @param $data
     * @param $fromYandexPayment
     * @throws \Exception
     */
    public function setProPaymentReceivedButNotPayedException($data, $fromYandexPayment)
    {
        $payment = $this->getMockBuilder(Payment::class)->disableOriginalConstructor()->getMock();
        $payment->method('isPaid')->willReturn(false);
        $paymentFetcher = $this->getMockBuilder(PaymentFetcher::class)->disableOriginalConstructor()->getMock();
        $paymentFetcher->method('fetch')->willReturn($fromYandexPayment);
        $repository = $this->getMockBuilder(ObjectRepository::class)->disableOriginalConstructor()->getMock();
        $repository->method('findOneBy')->willReturn($payment);
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $entityManager->method('getRepository')->willReturn($repository);
        $userService = $this->getMockBuilder(UserService::class)->disableOriginalConstructor()->getMock();

        $processor = new PaymentProcessor($paymentFetcher, $entityManager, $userService);
        $this->expectException(Exception\PaymentReceivedButNotPayedException::class);
        $processor->setProPayment($data);
    }

    /**
     * @return array
     */
    public function setProPaymentReceivedButNotPayedExceptionProvider(): array
    {
        return [
            [['object' => ['id' => 1]], ['status' => '1', 'paid' => true]],
            [['object' => ['id' => 1]], ['status' => Payment::STATUS_SUCCEEDED, 'paid' => false]],
            [['object' => ['id' => 1]], ['status' => Payment::STATUS_PENDING, 'paid' => false]],
        ];
    }

    /**
     * @test
     * @dataProvider setProSuccessProvider
     * @param $data
     * @param $fromYandexPayment
     * @throws \Exception
     */
    public function setProSuccess($data, $fromYandexPayment)
    {
        $payment = $this->getMockBuilder(Payment::class)->disableOriginalConstructor()->getMock();
        $payment->method('isPaid')->willReturn(false);
        $user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();
        $payment = new Payment();
        $payment->setPaid(false);
        $payment->setStatus(Payment::STATUS_PENDING);
        $payment->setUser($user);
        $paymentFetcher = $this->getMockBuilder(PaymentFetcher::class)->disableOriginalConstructor()->getMock();
        $paymentFetcher->method('fetch')->willReturn($fromYandexPayment);
        $repository = $this->getMockBuilder(ObjectRepository::class)->disableOriginalConstructor()->getMock();
        $repository->method('findOneBy')->willReturn($payment);
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $entityManager->method('getRepository')->willReturn($repository);
        $userService = $this->getMockBuilder(UserService::class)->disableOriginalConstructor()->getMock();

        $processor = new PaymentProcessor($paymentFetcher, $entityManager, $userService);
        $this->assertTrue($processor->setProPayment($data));
        $this->assertTrue($payment->isPaid());
        $this->assertEquals($payment->getStatus(), Payment::STATUS_SUCCEEDED);
    }

    /**
     * @return array
     */
    public function setProSuccessProvider(): array
    {
        return [
            [['object' => ['id' => 1]], ['status' => Payment::STATUS_SUCCEEDED, 'paid' => true]],
        ];
    }

}
