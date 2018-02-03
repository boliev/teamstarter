<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectProgress;
use AppBundle\Form\ProjectCreate\NameType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectCreateController extends AbstractController
{
    /**
     * @Route("/project/create/rules/", name="project_create_rules")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function rulesAction(Request $request)
    {
        $rulesText = $this->renderView('texts/en/project_rules.html.twig');

        return $this->render('project/create/rules/index.html.twig', [
            'rulesText' => $rulesText,
        ]);
    }

    /**
     * @Route("/project/create/name/", name="project_create_name")
     * @Route("/project/edit/{project}/name/", name="project_edit_name")
     *
     * @param Project|null $project
     * @param Request      $request
     *
     * @return Response
     */
    public function nameAction(Project $project = null, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$project) {
            $project = new Project();
            $progress = $em->getRepository(ProjectProgress::class)->find(ProjectProgress::UNFINISHED);
            $project->setProgress($progress);
        }

        $form = $this->createForm(NameType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($project);
            $em->flush();
        }

        return $this->render(':project/create/name:index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
