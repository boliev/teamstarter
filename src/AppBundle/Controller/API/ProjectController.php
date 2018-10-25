<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectOpenRole;
use AppBundle\Repository\ProjectOpenRoleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/api/v1/project/")
 */
class ProjectController extends ApiController
{
    /**
     * @Route("{project}/get-roles", name="api_v1_project_get_roles")
     * @Method("GET")
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
