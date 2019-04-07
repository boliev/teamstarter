<?php

namespace App\EventListener;

use App\Entity\User;
use App\Notifications\Notificator;
use App\Subscriber\Subscriber;
use Doctrine\ORM\NonUniqueResultException;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegistrationConfirmedListener implements EventSubscriberInterface
{
    /** @var Notificator  */
    private $notificator;

    /** @var Subscriber  */
    private $subscriber;

    public function __construct(Notificator $notificator, Subscriber $subscriber)
    {
        $this->notificator = $notificator;
        $this->subscriber = $subscriber;
    }

    /**
     * @param FilterUserResponseEvent $event
     * @throws NonUniqueResultException
     */
    public function onRegistrationSuccess(FilterUserResponseEvent $event)
    {
        /** @var User $user */
        $user = $event->getUser();
        $this->notificator->registrationSuccess($user);
        $this->subscriber->subscribeToDigest($user);
    }

    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_CONFIRMED => [
                ['onRegistrationSuccess', -10],
            ],
        ];
    }
}
