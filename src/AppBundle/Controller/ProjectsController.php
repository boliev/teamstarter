<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProjectsController extends Controller
{
    /**
     * @Route("/projects/{page}", name="projects_list", defaults={"page": 1})
     *
     * @param EntityManagerInterface $entityManager
     * @param int                    $page
     *
     * @return Response
     */
    public function indexActionEntityManagerInterface(EntityManagerInterface $entityManager, int $page = 1)
    {
        $projectRepository = $entityManager->getRepository(Project::class);
        $projects = $projectRepository->getPublishedQuery();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $projects,
            $page
        );

        return $this->render('project/list/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
