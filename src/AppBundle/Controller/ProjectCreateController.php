<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Form\ProjectCreate\MainInfoType;
use AppBundle\Service\ProjectService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class ProjectCreateController extends AbstractController
{
    /**
     * @Route("/project/create/rules/", name="project_create_rules")
     *
     * @return Response
     */
    public function rulesAction()
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
     * @param Project|null        $project
     * @param Request             $request
     * @param TranslatorInterface $translator
     * @param ProjectService      $projectService
     *
     * @return Response
     */
    public function mainAction(
        Project $project = null,
        Request $request,
        TranslatorInterface $translator,
        ProjectService $projectService
    ) {
        $em = $this->getDoctrine()->getManager();
        if (!$project) {
            $project = new Project();
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
            $projectService->reModerateIfNeeded($project);

            return $this->redirectToRoute('project_edit_open_vacancies_list', ['project' => $project->getId()]);
        }

        if (Project::STATUS_PUBLISHED === $project->getProgressStatus()) {
            $this->addFlash('project-warning', $translator->trans('project.re_moderation_warning'));
        }

        return $this->render(':project/create:main.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/project/edit/{project}/finish/", name="project_edit_finish")
     *
     * @param Project             $project
     * @param ProjectService      $projectService
     * @param TranslatorInterface $translator
     *
     * @return Response
     */
    public function finishAction(
        Project $project,
        ProjectService $projectService,
        TranslatorInterface $translator
    ) {
        if ($project->getUser()->getId() !== $this->getUser()->getId()) {
            $this->redirectToRoute('homepage');
        }

        if (!$projectService->sendToModerate($project)) {
            $this->addFlash('project-saved', $translator->trans('project.saved'));
        }

        return $this->redirectToRoute('user_projects_list');
    }
}
