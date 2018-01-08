<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Skill;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SkillsController extends AbstractController
{
    /**
     * @Route("/skills", name="ajax_skills")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedException();
        }

        $skills = $this->getDoctrine()->getRepository(Skill::class)->findAll();
        $result = [];
        foreach($skills as $skill) {
            $result[] = [
                'name' => $skill->getTitle()
            ];
        }

        return new JsonResponse($result);
    }
}
