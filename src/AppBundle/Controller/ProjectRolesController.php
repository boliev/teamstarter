<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectOpenRole;
use AppBundle\Entity\ProjectOpenRoleSkills;
use AppBundle\Form\ProjectCreate\OpenRoleType;
use AppBundle\Service\ProjectService;
use AppBundle\Service\SkillService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class ProjectRolesController extends Controller
{
    /**
     * @Route("/project/edit/{project}/open-roles/", name="project_edit_open_roles_list")
     *
     * @param Project             $project
     * @param TranslatorInterface $translator
     *
     * @return Response
     */
    public function rolesList(Project $project, TranslatorInterface $translator)
    {
        if ($project->getUser()->getId() !== $this->getUser()->getId()) {
            $this->redirectToRoute('homepage');
        }

        if (Project::STATUS_PUBLISHED === $project->getProgressStatus()) {
            $this->addFlash('project-warning', $translator->trans('project.re_moderation_warning'));
        }

        return $this->render(':project/create:roles_list.html.twig', [
            'project' => $project,
            'rolesList' => $project->getOpenRoles(),
        ]);
    }

    /**
     * @Route("/project/edit/{project}/open-roles/add", name="project_edit_open_roles_add")
     * @Route("/project/edit/{project}/open-roles/edit/{role}", defaults={"role"=null}, name="project_edit_open_roles_edit")
     *
     * @param Project                $project
     * @param ProjectOpenRole        $role
     * @param Request                $request
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $entityManager
     * @param SkillService           $skillService
     * @param ProjectService         $projectService
     *
     * @return Response
     */
    public function addRole(
        Project $project,
        ?ProjectOpenRole $role,
        Request $request,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        SkillService $skillService,
        ProjectService $projectService
    ) {
        if ($project->getUser()->getId() !== $this->getUser()->getId()) {
            $this->redirectToRoute('homepage');
        }

        if (!$role || 'project_edit_open_roles_add' === $request->get('_route')) {
            $role = new ProjectOpenRole();
            $role->setProject($project);
        }

        $form = $this->createForm(OpenRoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($role);
            $entityManager->flush();

            $skills = explode(',', $form->get('skills')->getData());
            $this->saveSkills($skills, $role, $skillService, $entityManager);
            $projectService->reModerateIfNeeded($project);

            if ('project_edit_open_roles_add' === $request->get('_route')) {
                $message = $translator->trans('project.add_role_success_add');
            } else {
                $message = $translator->trans('project.add_role_success_edit');
            }
            $this->addFlash('add-role-success', $message);

            return $this->redirectToRoute('project_edit_open_roles_list', ['project' => $project->getId()]);
        }

        return $this->render(':project/create:roles_list_add.html.twig', [
            'project' => $project,
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/project/open-roles/edit/{role}/close", name="project_role_edit_close")
     *
     * @param ProjectOpenRole        $role
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface    $translator
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function closeAction(ProjectOpenRole $role, EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        if ($role->getProject()->getUser()->getId() !== $this->getUser()->getId()) {
            $this->redirectToRoute('homepage');
        }

        $role->setVacant(false);
        $entityManager->persist($role);
        $entityManager->flush();

        $this->addFlash('add-role-success', $translator->trans('project.add_role_success_close'));

        return $this->redirectToRoute('project_edit_open_roles_list', ['project' => $role->getProject()->getId()]);
    }

    /**
     * @Route("/project/open-roles/edit/{role}/open", name="project_role_edit_open")
     *
     * @param ProjectOpenRole        $role
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface    $translator
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function openAction(ProjectOpenRole $role, EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        if ($role->getProject()->getUser()->getId() !== $this->getUser()->getId()) {
            $this->redirectToRoute('homepage');
        }

        $role->setVacant(true);
        $entityManager->persist($role);
        $entityManager->flush();

        $this->addFlash('add-role-success', $translator->trans('project.add_role_success_open'));

        return $this->redirectToRoute('project_edit_open_roles_list', ['project' => $role->getProject()->getId()]);
    }

    /**
     * @param array                  $skills
     * @param ProjectOpenRole        $projectOpenRole
     * @param SkillService           $skillService
     * @param EntityManagerInterface $em
     */
    private function saveSkills(array $skills, ProjectOpenRole $projectOpenRole, SkillService $skillService, EntityManagerInterface $em): void
    {
        $skillService->cleanSkillsForProjectOpenRole($projectOpenRole);
        foreach ($skills as $priority => $skill) {
            $skillEntity = $skillService->getOrCreateSkill($skill);
            $projectOpenRoleSkills = new ProjectOpenRoleSkills();
            $projectOpenRoleSkills->setOpenRole($projectOpenRole);
            $projectOpenRoleSkills->setSkill($skillEntity);
            $projectOpenRoleSkills->setPriority($priority);
            $em->persist($projectOpenRoleSkills);
            $em->flush();
        }
    }
}
