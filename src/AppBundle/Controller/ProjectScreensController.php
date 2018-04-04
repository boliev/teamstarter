<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectScreen;
use AppBundle\Entity\User;
use AppBundle\Service\ProjectService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectScreensController extends Controller
{
    /**
     * @Route("/project/edit/{project}/screens/", name="project_edit_screens")
     *
     * @param Project $project
     * @param Request $request
     *
     * @return Response
     */
    public function screensList(Project $project, Request $request)
    {
        if ($project->getUser()->getId() !== $this->getUser()->getId()) {
            $this->redirectToRoute('homepage');
        }

        return $this->render(':project/create:screens.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @Route("/project/edit/{project}/upload-screen/", name="project_edit_upload_screens")
     *
     * @param Project                $project
     * @param EntityManagerInterface $em
     * @param ProjectService         $projectService
     *
     * @return Response
     */
    public function uploadScreenAction(Project $project, EntityManagerInterface $em, ProjectService $projectService)
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($project->getUser()->getId() !== $user->getId()) {
            $this->redirectToRoute('homepage');
        }

        if (isset($_FILES['qqfile'])) {
            try {
                $file = $projectService->uploadScreen($project, $_FILES['qqfile']);
                $projectScreen = new ProjectScreen();
                $projectScreen->setProject($project);
                $projectScreen->setScreenshot($file);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => $e->getMessage()], 400);
            }

            $em->persist($projectScreen);
            $em->flush();

            return new JsonResponse(['success' => true, 'picture' => $file.'?'.mt_rand(0, 5000), 'screenId' => $projectScreen->getId()]);
        }
    }

    /**
     * @Route("/project/edit/delete-screen/{screen}", name="project_edit_delete_screen", methods="POST")
     *
     * @param ProjectScreen          $screen
     * @param EntityManagerInterface $em
     * @param ProjectService         $projectService
     *
     * @return Response
     */
    public function deleteScreenAction(ProjectScreen $screen, EntityManagerInterface $em, ProjectService $projectService)
    {
        /** @var User $user */
        $user = $this->getUser();
        $project = $screen->getProject();
        if ($project->getUser()->getId() !== $user->getId()) {
            $this->redirectToRoute('homepage');
        }

        $projectService->removeScreen($screen);

        return new JsonResponse();
    }
}
