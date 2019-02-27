<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Entity\UserSpecializations;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RegistrationIncompleteSubscriber implements EventSubscriberInterface
{
    const SPECIALIZATION_ROUTE = 'specify_specialization_form';
    const ABOUT_ROUTE = 'specify_about_form';
    const ABOUT_SKIP_ROUTE = 'specify_about_form_skip';
    const CONTACTS_ROUTE = 'specify_contacts_form';
    const PROMO_CODE_ROUTE = 'user_promo_code_form';
    const PROMO_CODE_SIGN_UP_ROUTE = 'user_promo_code_sign_up';
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
     * @param RouterInterface               $router
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, RouterInterface $router, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws \Exception
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->isNeeded($event)) {
            return;
        }

        /** @var User $user */
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;
        if (!$user) {
            return;
        }

        // Promo Code
        if (!$user->isAdmin() && null === $user->getPromoCode()) {
            $event->setResponse(new RedirectResponse($this->router->generate(self::PROMO_CODE_ROUTE)));

            return;
        }

        // Specializations
        $userSpecializations = $this->em->getRepository(UserSpecializations::class)->findBy(['user' => $user]);
        if (count($userSpecializations) < 1) {
            $event->setResponse(new RedirectResponse($this->router->generate(self::SPECIALIZATION_ROUTE)));

            return;
        }

        if (null === $user->getCountry()) {
            $event->setResponse(new RedirectResponse($this->router->generate(self::ABOUT_ROUTE)));

            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            // must be registered after the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 0)),
        );
    }

    /**
     * @param GetResponseEvent $event
     *
     * @return bool
     */
    private function isNeeded(GetResponseEvent $event): bool
    {
        if ($event->getRequest()->isXmlHttpRequest()) {
            return false;
        }
        $route = $event->getRequest()->get('_route');
        if (in_array($route, [
            self::ABOUT_ROUTE,
            self::SPECIALIZATION_ROUTE,
            self::ABOUT_SKIP_ROUTE,
            self::CONTACTS_ROUTE,
            self::PROMO_CODE_ROUTE,
            self::PROMO_CODE_SIGN_UP_ROUTE,
        ])) {
            return false;
        }

        try {
            $isLoggedIn = $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY');
            if (!$isLoggedIn) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
