<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserProjectsListController extends AbstractController
{
    /**
     * @Route("/user/projects", name="user_projects_list")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $projects = $user->getProjects();

        return $this->render('user/projects/projects_list.html.twig', [
            'projectsList' => $projects,
        ]);
    }

    /**
     * @Route("/user/projects/{project}/moderator-comments", name="user_projects_moderator_comments")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function moderatorCommentsAction(Project $project)
    {
        $comments = $project->getModeratorsComments();
        if($project->getUser()->getId() !== $this->getUser()->getId() || count($comments) < 1) {
            $this->redirectToRoute('user_projects_list');
        }

        return $this->render('user/projects/projects_moderator_comments.html.twig', [
            'comments' => $comments,
        ]);
    }
}
