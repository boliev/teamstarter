<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectOpenVacancy;
use AppBundle\Entity\ProjectOpenVacancySkills;
use AppBundle\Form\ProjectCreate\OpenVacancyType;
use AppBundle\Service\ProjectService;
use AppBundle\Service\SkillService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class ProjectVacanciesController extends Controller
{
    /**
     * @Route("/project/edit/{project}/open-vacancies/", name="project_edit_open_vacancies_list")
     *
     * @param Project             $project
     * @param TranslatorInterface $translator
     *
     * @return Response
     */
    public function vacanciesList(Project $project, TranslatorInterface $translator)
    {
        if ($project->getUser()->getId() !== $this->getUser()->getId()) {
            $this->redirectToRoute('homepage');
        }

        if (Project::STATUS_PUBLISHED === $project->getProgressStatus()) {
            $this->addFlash('project-warning', $translator->trans('project.re_moderation_warning'));
        }

        return $this->render(':project/create:vacancies_list.html.twig', [
            'project' => $project,
            'vacanciesList' => $project->getOpenVacancies(),
        ]);
    }

    /**
     * @Route("/project/edit/{project}/open-vacancies/add", name="project_edit_open_vacancies_add")
     * @Route("/project/edit/{project}/open-vacancies/edit/{vacancy}", defaults={"vacancy"=null}, name="project_edit_open_vacancies_edit")
     *
     * @param Project                $project
     * @param ProjectOpenVacancy     $vacancy
     * @param Request                $request
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $entityManager
     * @param SkillService           $skillService
     * @param ProjectService         $projectService
     *
     * @return Response
     */
    public function addVacancy(
        Project $project,
        ?ProjectOpenVacancy $vacancy,
        Request $request,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        SkillService $skillService,
        ProjectService $projectService
    ) {
        if ($project->getUser()->getId() !== $this->getUser()->getId()) {
            $this->redirectToRoute('homepage');
        }

        if (!$vacancy || 'project_edit_open_vacancies_add' === $request->get('_route')) {
            $vacancy = new ProjectOpenVacancy();
            $vacancy->setProject($project);
        }

        $form = $this->createForm(OpenVacancyType::class, $vacancy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($vacancy);
            $entityManager->flush();

            $skills = explode(',', $form->get('skills')->getData());
            $this->saveSkills($skills, $vacancy, $skillService, $entityManager);
            $projectService->reModerateIfNeeded($project);

            if ('project_edit_open_vacancies_add' === $request->get('_route')) {
                $message = $translator->trans('project.add_vacancy_success_add');
            } else {
                $message = $translator->trans('project.add_vacancy_success_edit');
            }
            $this->addFlash('add-vacancy-success', $message);

            return $this->redirectToRoute('project_edit_open_vacancies_list', ['project' => $project->getId()]);
        }

        return $this->render(':project/create:vacancies_list_add.html.twig', [
            'project' => $project,
            'vacancy' => $vacancy,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/project/open-vacancies/edit/{vacancy}/close", name="project_vacancy_edit_close")
     *
     * @param ProjectOpenVacancy     $vacancy
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface    $translator
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function closeAction(ProjectOpenVacancy $vacancy, EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        if ($vacancy->getProject()->getUser()->getId() !== $this->getUser()->getId()) {
            $this->redirectToRoute('homepage');
        }

        $vacancy->setVacant(false);
        $entityManager->persist($vacancy);
        $entityManager->flush();

        $this->addFlash('add-vacancy-success', $translator->trans('project.add_vacancy_success_close'));

        return $this->redirectToRoute('project_edit_open_vacancies_list', ['project' => $vacancy->getProject()->getId()]);
    }

    /**
     * @Route("/project/open-vacancies/edit/{vacancy}/open", name="project_vacancy_edit_open")
     *
     * @param ProjectOpenVacancy     $vacancy
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface    $translator
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function openAction(ProjectOpenVacancy $vacancy, EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        if ($vacancy->getProject()->getUser()->getId() !== $this->getUser()->getId()) {
            $this->redirectToRoute('homepage');
        }

        $vacancy->setVacant(true);
        $entityManager->persist($vacancy);
        $entityManager->flush();

        $this->addFlash('add-vacancy-success', $translator->trans('project.add_vacancy_success_open'));

        return $this->redirectToRoute('project_edit_open_vacancies_list', ['project' => $vacancy->getProject()->getId()]);
    }

    /**
     * @param array                  $skills
     * @param ProjectOpenVacancy     $vacancy
     * @param SkillService           $skillService
     * @param EntityManagerInterface $em
     */
    private function saveSkills(array $skills, ProjectOpenVacancy $vacancy, SkillService $skillService, EntityManagerInterface $em): void
    {
        $skillService->cleanSkillsForProjectOpenVacancy($vacancy);
        foreach ($skills as $priority => $skill) {
            $skillEntity = $skillService->getOrCreateSkill($skill);
            $vacancySkill = new ProjectOpenVacancySkills();
            $vacancySkill->setVacancy($vacancy);
            $vacancySkill->setSkill($skillEntity);
            $vacancySkill->setPriority($priority);
            $em->persist($vacancySkill);
            $em->flush();
        }
    }
}
