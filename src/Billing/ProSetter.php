<?php

namespace App\Billing;

use App\Entity\StatisticBuys;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProSetter
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $fromEmailAddress;

    /**
     * @var string
     */
    private $fromName;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $notifyNewProUsersEmails;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $fromEmailAddress,
        string $fromName,
        \Swift_Mailer $mailer,
        string $notifyNewProUsersEmails,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->fromEmailAddress = $fromEmailAddress;
        $this->fromName = $fromName;
        $this->mailer = $mailer;
        $this->notifyNewProUsersEmails = explode(',', $notifyNewProUsersEmails);
        $this->translator = $translator;
    }

    /**
     * @param User $user
     *
     * @throws \Exception
     */
    public function setUser(User $user)
    {
        $nowDate = new \DateTime();
        $nowDate->add(new \DateInterval('P1M'));
        $user->setProUntil($nowDate);
        $this->entityManager->persist($user);

        $statistic = new StatisticBuys();
        $statistic->setUser($user);
        $this->entityManager->persist($statistic);

        $this->entityManager->flush();

        $message = (new \Swift_Message($this->translator->trans('pro.buy_success_email.subject')))
            ->setFrom($this->fromEmailAddress, $this->fromName)
            ->setTo($user->getEmail())
            ->setBody($this->translator->trans('pro.buy_success_email.message', ['%username%' => $user->getFirstName() ?? $user->getEmail()]), 'text/html');

        $this->mailer->send($message);

        $message = (new \Swift_Message($this->translator->trans('pro.buy_success_email_admins.subject')))
            ->setFrom($this->fromEmailAddress, $this->fromName)
            ->setTo($this->notifyNewProUsersEmails)
            ->setBody($this->translator->trans('pro.buy_success_email_admins.message'), 'text/html');

        $this->mailer->send($message);
    }
}
