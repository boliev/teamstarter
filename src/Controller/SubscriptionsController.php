<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Subscriber\Subscriber;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionsController extends AbstractController
{
    /**
     * @Route("subscribe_to_digest", name="subscribe_to_digest", methods={"POST"})
     *
     * @param Subscriber $subscriber
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function subscribeToDigest(Subscriber $subscriber)
    {
        $user = $this->getUser();
        if(!$user) {
            throw new NotFoundHttpException();
        }
        $subscriber->subscribeToDigest($this->getUser());

        return new JsonResponse(['result' => 'OK']);

    }

    /**
     * @Route("/unsubscribe/{hash}/page", name="unsubscribe_page")
     *
     * @param string $hash
     * @param UserRepository $userRepository
     * @return Response
     */
    public function unsubscribePage(string $hash, UserRepository $userRepository)
    {
        $user = $userRepository->getUserByUnsubscribeHash($hash);
        if(!$user) {
            throw new NotFoundHttpException();
        }

        return $this->render('subscriptions/unsubscribe/index.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/unsubscribe/{hash}/action", name="unsubscribe_action")
     *
     * @param string $hash
     * @param Subscriber $subscriber
     * @return void
     */
    public function unsubscribeAction(string $hash, Subscriber $subscriber)
    {

    }
}