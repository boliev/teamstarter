<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Offer;
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
            new TwigFunction('userNewMessagesCount', array($this, 'getUserMessagesCount')),
            new TwigFunction('offerNewMessagesCount', array($this, 'getOfferMessagesCount')),
        );
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return int|null
     */
    public function getUserMessagesCount()
    {
        if (null === $this->user) {
            return null;
        }

        return $this->messageRepository->getUserNewMessagesCount($this->user);
    }

    /**
     * @param Offer $offer
     *
     * @return int|null
     */
    public function getOfferMessagesCount(Offer $offer)
    {
        if (null === $this->user) {
            return null;
        }

        return $this->messageRepository->getOfferNewMessagesCount($offer, $this->user);
    }
}
