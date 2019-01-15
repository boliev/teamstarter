<?php

namespace App\Controller;

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
}
