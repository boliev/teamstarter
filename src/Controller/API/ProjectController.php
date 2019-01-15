<?php

namespace App\Controller\API;

use App\Entity\Project;
use App\Entity\ProjectOpenRole;
use App\Repository\ProjectOpenRoleRepository;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/project/")
 */
class ProjectController extends ApiController
{
    /**
     * @Route("{project}/get-roles", name="api_v1_project_get_roles", methods={"GET"})
     *
     * @param Project                   $project
     * @param ProjectOpenRoleRepository $repository
     *
     * @return JsonResponse
     */
    public function indexAction(
        Project $project,
        ProjectOpenRoleRepository $repository
    ) {
        if (!$this->getUser() || $project->getUser()->getId() !== $this->getUser()->getId()) {
            throw new AccessDeniedException();
        }
        $rolesObjects = $repository->getVacantForProjectBuilder($project)->getQuery()->getResult();
        $roles = [];

        foreach ($rolesObjects as $ro) {
            /* @var ProjectOpenRole $ro */
            $roles[$ro->getId()] = $ro->getName();
        }

        return new JsonResponse($roles);
    }
}
