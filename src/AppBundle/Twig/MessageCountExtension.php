<?php

namespace AppBundle\Twig;

use AppBundle\Entity\User;
use AppBundle\Repository\MessageRepository;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MessageCountExtension extends AbstractExtension
{
    /**
     * @var MessageRepository
     */
    private $messageRepository;

    /**
     * @var User|null
     */
    private $user;

    public function __construct(MessageRepository $messageRepository, Security $security)
    {
        $this->messageRepository = $messageRepository;
        $this->user = $security->getUser();
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('messageCount', array($this, 'getMessageCount')),
        );
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return int|null
     */
    public function getMessageCount()
    {
        if (null === $this->user) {
            return null;
        }

        return $this->messageRepository->getNewMessagesCount($this->user);
    }
}
