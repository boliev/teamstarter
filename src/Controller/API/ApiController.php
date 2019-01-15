<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    protected function error($text = '', $code = Response::HTTP_BAD_REQUEST)
    {
        return new JsonResponse($text, $code);
    }
}
