<?php

namespace App\Billing;

use App\Entity\Payment;
use App\Entity\User;
use App\Notifications\Notificator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentCreator
{
    const RUB = 'RUB';

    /** @var PaymentClient */
    private $client;

    /** @var int */
    private $proPrice;

    /** @var RouterInterface */
    private $router;

    /** @var TranslatorInterface */
    private $translator;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var Notificator  */
    private $notificator;

    public function __construct(
        int $proPrice,
        PaymentClient $client,
        RouterInterface $router,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        Notificator $notificator
    ) {
        $this->client = $client;
        $this->proPrice = $proPrice;
        $this->router = $router;
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        $this->notificator = $notificator;
    }

    /**
     * @param User $user
     *
     * @return Payment|null
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function createPaymentForPro(User $user): ?Payment
    {
        try {
            $rawPayment = $this->client->createPayment(
                $this->proPrice,
                self::RUB,
                $this->router->generate('pro_checkout_success', [], RouterInterface::ABSOLUTE_URL),
                $this->translator->trans('pro.payment_description')
            );
        } catch (\Exception $e) {
            $this->notificator->paymentCreateError($user, $e);
            return null;
        }

        $payment = new Payment();
        $payment->setExtId($rawPayment['id']);
        $payment->setStatus($rawPayment['status']);
        $payment->setUser($user);
        $payment->setAmount($this->proPrice);
        $payment->setCurrency(self::RUB);
        $payment->setCreatedAt(new \DateTime($rawPayment['created_at']));
        $payment->setDescription($rawPayment['description']);
        $payment->setPaid($rawPayment['paid']);
        $payment->setMetadata($rawPayment['metadata']);
        $payment->setType(Payment::TYPE_YANDEX);
        $payment->setConfirmUrl($rawPayment['confirmation']['confirmation_url']);
        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        return $payment;
    }
}
