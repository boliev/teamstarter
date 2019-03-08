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
        $proUntil = $this->getUntilDate($user);
        $user->setProUntil($proUntil);
        $this->entityManager->persist($user);
        $this->setToStatistic($user, $proUntil);
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

    /**
     * @param User $user
     * @return \DateTime|null
     * @throws \Exception
     */
    private function getUntilDate(User $user)
    {
        $userProDate = $user->getProUntil();
        if (null === $userProDate) {
            $userProDate =  new \DateTime();
        }

        $userProDate->add(new \DateInterval('P1M'));
        return clone $userProDate;
    }

    /**
     * @param User      $user
     * @param \DateTime $proUntil
     *
     * @throws \Exception
     */
    private function setToStatistic(User $user, \DateTime $proUntil)
    {
        $statistic = new StatisticBuys();
        $statistic->setUser($user);
        $statistic->setUntil($proUntil);
        $this->entityManager->persist($statistic);
    }
}
