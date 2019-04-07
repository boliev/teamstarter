<?php

namespace App\Controller;

use App\Subscriber\Subscriber;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
}