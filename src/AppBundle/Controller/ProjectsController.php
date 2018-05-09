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
     * @Route("/projects", name="projects_list")
     *
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    public function indexAction(EntityManagerInterface $entityManager)
    {
        $projectRepository = $entityManager->getRepository(Project::class);
        $projects = $projectRepository->getPublished();

        return $this->render('project/list/index.html.twig', [
            'projects' => $projects,
        ]);
    }
}
