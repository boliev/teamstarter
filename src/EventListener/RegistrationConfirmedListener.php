<?php

namespace App\EventListener;

use App\Notifications\Notificator;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegistrationConfirmedListener implements EventSubscriberInterface
{
    /** @var Notificator  */
    private $notificator;

    public function __construct(Notificator $notificator)
    {
        $this->notificator = $notificator;
    }
    public function onRegistrationSuccess(FilterUserResponseEvent $event)
    {
        $this->notificator->registrationSuccess($event->getUser());
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
