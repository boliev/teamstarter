<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\UserSpecializations;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RegistrationIncompleteSubscriber implements EventSubscriberInterface
{
    const SPECIALIZATION_ROUTE = 'specialization_form';
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var Router
     */
    private $router;

    /**
     * RegistrationIncompleteSubscriber constructor.
     *
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface  $tokenStorage
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, Router $router)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (self::SPECIALIZATION_ROUTE === $event->getRequest()->get('_route')) {
            return;
        }
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;
        if (!$user) {
            return;
        }

        $userSpecializations = $this->em->getRepository(UserSpecializations::class)->findBy(['user' => $user]);
        if (count($userSpecializations) < 1) {
            $event->setResponse(new RedirectResponse($this->router->generate(self::SPECIALIZATION_ROUTE)));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered after the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 0)),
        );
    }
}
