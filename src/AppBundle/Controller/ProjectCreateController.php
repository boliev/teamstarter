<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectDoc;
use AppBundle\Entity\ProjectProgress;
use AppBundle\Form\ProjectCreate\DocType;
use AppBundle\Form\ProjectCreate\MainInfoType;
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

        return $this->render(':project/create:rules.html.twig', [
            'rulesText' => $rulesText,
        ]);
    }

    /**
     * @Route("/project/create/main/", name="project_create_name")
     * @Route("/project/edit/{project}/main/", name="project_edit_name")
     *
     * @param Project|null $project
     * @param Request      $request
     *
     * @return Response
     */
    public function mainAction(Project $project = null, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$project) {
            $project = new Project();
            $progress = $em->getRepository(ProjectProgress::class)->find(ProjectProgress::UNFINISHED);
            $project->setProgress($progress);
            $project->setUser($this->getUser());
        }

        if ($project->getUser()->getId() !== $this->getUser()->getId()) {
            $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(MainInfoType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($project);
            $em->flush();

            return $this->redirectToRoute('project_edit_docs', ['project' => $project->getId()]);
        }

        return $this->render(':project/create:main.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/project/edit/{project}/docs/", name="project_edit_docs")
     *
     * @param Project $project
     * @param Request $request
     *
     * @return Response
     */
    public function docsAction(Project $project, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($project->getUser()->getId() !== $this->getUser()->getId()) {
            $this->redirectToRoute('homepage');
        }

        $doc = $project->getDocs()->first();
        if (!$doc) {
            $doc = new ProjectDoc();
            $doc->setProject($project);
        }

        $form = $this->createForm(DocType::class, $doc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($doc);
            $em->flush();

            return $this->redirectToRoute('project_edit_docs', ['project' => $project->getId()]);
        }

        return $this->render(':project/create:docs.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
        ]);
    }
}
