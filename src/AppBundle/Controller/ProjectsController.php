<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Search\ProjectSearcher\ProjectSearcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectsController extends Controller
{
    /**
     * @Route("/projects/{page}", name="projects_list", defaults={"page": 1})
     *
     * @param Request                  $request
     * @param EntityManagerInterface   $entityManager
     * @param ProjectSearcherInterface $projectSearcher
     * @param int                      $page
     *
     * @return Response
     */
    public function indexAction(
        Request $request,
        EntityManagerInterface $entityManager,
        ProjectSearcherInterface $projectSearcher,
        int $page = 1
    ) {
        $ids = null;
        $searchQuery = $request->query->get('query');
        if (null !== $searchQuery && '' !== $searchQuery) {
            $ids = $projectSearcher->search($searchQuery);
        }

        $projectRepository = $entityManager->getRepository(Project::class);
        $projects = $projectRepository->getPublishedQuery($ids);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $projects,
            $page
        );

        return $this->render('project/list/index.html.twig', [
            'pagination' => $pagination,
            'searchQuery' => $searchQuery,
        ]);
    }
}
