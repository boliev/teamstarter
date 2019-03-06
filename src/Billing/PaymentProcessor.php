<?php

namespace App\Billing;

use App\Entity\Payment;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PaymentProcessor
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserService */
    private $userService;

    /** @var PaymentFetcher */
    private $paymentFetcher;

    public function __construct(
        PaymentFetcher $paymentFetcher,
        EntityManagerInterface $entityManager,
        UserService $userService
    ) {
        $this->entityManager = $entityManager;
        $this->userService = $userService;
        $this->paymentFetcher = $paymentFetcher;
    }

    /**
     * @param array|null $data
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function setProPayment(?array $data)
    {
        $id = $this->getPaymentIdFromRequest($data);
        $payment = $this->getPaymentEntity($id);
        $paymentFromYandex = $this->getPaymentFromYandex($id);

        if (Payment::STATUS_SUCCEEDED === $paymentFromYandex['status'] && true === $paymentFromYandex['paid']) {
            $this->userService->setPro($payment->getUser());
            $this->setPaymentPayed($payment, $paymentFromYandex['status']);

            return true;
        }
        throw new BadRequestHttpException();
    }

    private function getPaymentIdFromRequest(?array $data): string
    {
        if (!isset($data['object']['id'])) {
            throw new BadRequestHttpException();
        }

        return $data['object']['id'];
    }

    private function getPaymentEntity(string $id): Payment
    {
        /** @var Payment $payment */
        $payment = $this->entityManager->getRepository(Payment::class)->findOneBy(['extId' => $id]);
        if (!$payment) {
            throw new BadRequestHttpException();
        }

        if ($payment->isPaid()) {
            throw new BadRequestHttpException();
        }

        return $payment;
    }

    private function getPaymentFromYandex(string $id): array
    {
        $paymentFromYandex = $this->paymentFetcher->fetch($id);
        if (!$paymentFromYandex) {
            throw new BadRequestHttpException();
        }

        if (isset($paymentFromYandex['status']) && $paymentFromYandex['paid']) {
            return $paymentFromYandex;
        }

        throw new BadRequestHttpException();
    }

    private function setPaymentPayed(Payment $payment, string $status)
    {
        $payment->setStatus($status);
        $payment->setPaid(true);
        $this->entityManager->persist($payment);
        $this->entityManager->flush();
    }
}
