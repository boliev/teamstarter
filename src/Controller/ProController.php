<?php

namespace App\Controller;

use App\Service\UserService;
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
     * @param UserService $userService
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function checkoutAction(UserService $userService)
    {
        $userService->setPro($this->getUser());

        return $this->render('pro/checkout/success.html.twig', []);
    }
}
