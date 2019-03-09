<?php

namespace App\Controller;

use App\Billing\PaymentCreator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProController extends AbstractController
{
    /**
     * @Route("/pro/", name="pro_buy_page")
     *
     * @param int $price
     *
     * @return Response
     */
    public function indexAction(int $price)
    {
        return $this->render('pro/index/index.html.twig', ['price' => $price]);
    }

    /**
     * @Route("/pro/checkout/", name="pro_checkout")
     *
     * @param PaymentCreator $paymentCreator
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkoutAction(PaymentCreator $paymentCreator)
    {
        $payment = $paymentCreator->createPaymentForPro($this->getUser());

        if (!$payment) {
            return $this->render('pro/checkout/error.html.twig');
        }

        return $this->redirect($payment->getConfirmUrl());
    }

    /**
     * @Route("/pro/checkout/success", name="pro_checkout_success")
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function checkoutSuccessAction()
    {
        return $this->render('pro/checkout/success.html.twig');
    }
}
