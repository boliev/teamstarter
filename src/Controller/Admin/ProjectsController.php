<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Entity\ProjectModeratorComments;
use App\Repository\ProjectRepository;
use App\Service\ProjectService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProjectsController extends EasyAdminController
{
    /**
     * @Route("/admin/approve", name="dialogs_list")
     */
    public function approveAction()
    {

    }

    /**
     * @Route("/admin/project/decline", name="admin_project_decline")
     *
     * @param Request $request
     * @param ProjectRepository $projectRepository
     * @param EntityManagerInterface $entityManager
     *
     * @param ProjectService $projectService
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function declineAction(
        Request $request,
        ProjectRepository $projectRepository,
        EntityManagerInterface $entityManager,
        ProjectService $projectService
    )
    {
        $reason = $request->request->get('reason');
        $projectId = $request->request->get('projectId');
        /** @var Project $project */
        $project = $projectRepository->find($projectId);
        if (null === $project) {
            throw new NotFoundHttpException('project not found');
        }

        $comment = new ProjectModeratorComments();
        $comment->setUser($this->getUser());
        $comment->setProject($project);
        $comment->setComment($reason);
        $entityManager->persist($comment);
        $entityManager->flush();

        $res = $projectService->decline($project);
        if(!$res) {
            return new JsonResponse(['error' => 'Can\'t apply status'], 400);
        }

        return new JsonResponse([], 200);
    }
}
