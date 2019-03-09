<?php

namespace App\Controller;

use App\Billing\Exception\BillingException;
use App\Billing\PaymentProcessor;
use App\Notifications\Notificator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BillingController extends AbstractController
{
    /**
     * @Route("/billing/yandex", name="billing_yandex", methods={"POST"})
     *
     * @param Request          $request
     * @param PaymentProcessor $paymentProcessor
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function EventYandexAction(Request $request, PaymentProcessor $paymentProcessor, Notificator $notificator)
    {
        $data = json_decode($request->getContent(), true);
        $response = new Response();
        try {
            $paymentProcessor->setProPayment($data);
            $response->setStatusCode(200);
        } catch (BillingException $e) {
            $response->setStatusCode(400);
            $notificator->paymentProcessError($data, $e);
        }

        return $response;
    }
}
