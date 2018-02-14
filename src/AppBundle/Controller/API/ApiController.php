<?php

namespace AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    protected function error($text = '', $code = Response::HTTP_BAD_REQUEST)
    {
        return new JsonResponse($text, $code);
    }
}
