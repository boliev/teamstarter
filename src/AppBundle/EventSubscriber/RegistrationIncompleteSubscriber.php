<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\User;
use AppBundle\Entity\UserSpecializations;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RegistrationIncompleteSubscriber implements EventSubscriberInterface
{
    const SPECIALIZATION_ROUTE = 'specify_specialization_form';
    const ABOUT_ROUTE = 'specify_about_form';
    const ABOUT_SKIP_ROUTE = 'specify_about_form_skip';
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
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * RegistrationIncompleteSubscriber constructor.
     *
     * @param EntityManagerInterface        $em
     * @param TokenStorageInterface         $tokenStorage
     * @param Router                        $router
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, Router $router, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequest()->isXmlHttpRequest()) {
            return;
        }
        if (in_array($event->getRequest()->get('_route'), [self::ABOUT_ROUTE, self::SPECIALIZATION_ROUTE, self::ABOUT_SKIP_ROUTE])) {
            return;
        }
        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return;
        }

        /** @var User $user */
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;
        if (!$user) {
            return;
        }

        $userSpecializations = $this->em->getRepository(UserSpecializations::class)->findBy(['user' => $user]);
        if (count($userSpecializations) < 1) {
            $event->setResponse(new RedirectResponse($this->router->generate(self::SPECIALIZATION_ROUTE)));
        }

        $now = new \DateTime();
        $diff = new \DateInterval('P7D');
        $skippedUntil = $user->getAboutFormSkipped()->add($diff);

        if (null === $user->getCountry() && $now > $skippedUntil) {
            $event->setResponse(new RedirectResponse($this->router->generate(self::ABOUT_ROUTE)));
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
