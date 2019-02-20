<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/terms", name="terms")
     *
     * @param Request $request
     * @param string $locale
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function termsAction(Request $request, string $locale, \Parsedown $parsedown)
    {
        $terms = $this->renderView('texts/'.$locale.'/terms.html.twig', [
            'domain' => $request->getHost(),
        ]);

        $terms = $parsedown->text($terms);

        return $this->render('default/terms.html.twig', [
            'domain' => $request->getHost(),
            'terms' => $terms
        ]);
    }

}
