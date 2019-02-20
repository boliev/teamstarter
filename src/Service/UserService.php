<?php

namespace App\Service;

use App\Entity\StatisticBuys;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Exception\LoaderLoadException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserService
{
    /**
     * @var string
     */
    private $kernelRoot;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var int
     */
    private $avatarMaxSize;

    /**
     * @var EntityManager
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
     * @var string
     */
    private $notifyNewProUsersEmails;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @param TranslatorInterface $translator
     * @param \Swift_Mailer $mailer
     * @param string $kernelRoot
     * @param int $avatarMaxSize
     * @param string $fromEmailAddress
     * @param string $fromName
     * @param string $notifyNewProUsersEmails
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        TranslatorInterface $translator,
        \Swift_Mailer $mailer,
        string $kernelRoot,
        int $avatarMaxSize,
        string $fromEmailAddress,
        string $fromName,
        string $notifyNewProUsersEmails,
        EntityManagerInterface $entityManager
    )
    {
        $this->kernelRoot = $kernelRoot;
        $this->translator = $translator;
        $this->avatarMaxSize = $avatarMaxSize;
        $this->entityManager = $entityManager;
        $this->fromEmailAddress = $fromEmailAddress;
        $this->fromName = $fromName;
        $this->notifyNewProUsersEmails = explode(',', $notifyNewProUsersEmails);
        $this->mailer = $mailer;
    }

    /**
     * @param User  $user
     * @param array $uploadedFileSource
     *
     * @return string
     *
     * @throws LoaderLoadException
     *
     * @internal param UploadedFile $avatar
     */
    public function uploadAvatar(User $user, array $uploadedFileSource): string
    {
        $avatar = new UploadedFile($uploadedFileSource['tmp_name'], $uploadedFileSource['name'], mime_content_type($uploadedFileSource['tmp_name']), $uploadedFileSource['size']);
        if ($this->avatarMaxSize < $avatar->getSize()) {
            throw new LoaderLoadException($this->translator->trans('user.avatar_max_size_exception', ['{size}' => round(($this->avatarMaxSize / 1000000), 1)]));
        }

        $extension = $avatar->guessClientExtension();
        if (!in_array(strtolower($extension), ['jpg', 'png', 'jpeg'])) {
            throw new LoaderLoadException($this->translator->trans('user.avatar_extension_exception'));
        }

        $fs = $this->getFS();
        $dir = substr(md5($user->getId()), 0, 2);

        if ($user->getProfilePicture() && $fs->exists($this->kernelRoot.'/public'.$user->getProfilePicture())) {
            $fs->remove($this->kernelRoot.'/../public'.$user->getProfilePicture());
        }
        $pictureName = $user->getId().'.'.$extension;
        $avatar->move($this->kernelRoot.'/../public/avatars/'.$dir.'/', $pictureName);

        return '/avatars/'.$dir.'/'.$pictureName;
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function setPro(User $user)
    {
        $nowDate = new \DateTime();
        $nowDate->add(new \DateInterval('P1M'));
        $user->setProUntil($nowDate);
        $this->entityManager->persist($user);

        $statistic = new StatisticBuys();
        $statistic->setUser($user);
        $this->entityManager->persist($statistic);

        $this->entityManager->flush($user);

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
     * @return Filesystem
     */
    private function getFS(): Filesystem
    {
        return new Filesystem();
    }
}
