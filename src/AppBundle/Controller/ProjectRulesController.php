<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProjectRulesController extends AbstractController
{
    /**
     * @Route("/project/rules/", name="project_rules")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rulesAction(Request $request)
    {
        $rulesText = $this->renderView('texts/en/project_rules.html.twig');

        return $this->render('project/rules/index.html.twig', [
            'rulesText' => $rulesText,
        ]);
    }
}
