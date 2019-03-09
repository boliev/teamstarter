<?php

namespace App\Controller;

use App\Billing\PaymentProcessor;
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
    public function EventYandexAction(Request $request, PaymentProcessor $paymentProcessor)
    {
        $data = json_decode($request->getContent(), true);
        $paymentProcessor->setProPayment($data);
        $response = new Response();
        $response->setStatusCode(200);

        return $response;
    }
}
