<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
    /**
     * @Route("/locale/{locale}", name="locale_change")
     *
     * @param string  $locale
     * @param Request $request
     */
    public function changeLocaleAction(string $locale, Request $request)
    {
        $locales = ['en', 'ru'];
        $response = new Response();
        if (in_array($locale, $locales)) {
            $response->headers->setCookie(new Cookie('_locale', $locale));
        }

        if ($request->headers->get('referer')) {
            $this->redirect($request->headers->get('referer'));
        } else {
            $this->redirect('/');
        }
    }
}
