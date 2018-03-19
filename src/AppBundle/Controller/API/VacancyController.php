<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\ProjectOpenVacancy;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v1/vacancy/")
 */
class VacancyController extends ApiController
{
    /**
     * @Route("{vacancy}/set-status", name="api_v1_vacancy_set_status")
     * @Method("POST")
     *
     * @param ProjectOpenVacancy     $vacancy
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     *
     * @return JsonResponse
     */
    public function indexAction(ProjectOpenVacancy $vacancy, Request $request, EntityManagerInterface $entityManager)
    {
        if ($this->getUser()->getId() !== $vacancy->getProject()->getUser()->getId()) {
            $this->redirectToRoute('homepage');
        }

        $status = $request->request->get('status');
        $vacancy->setVacant($status);
        $entityManager->persist($vacancy);
        $entityManager->flush();

        return new JsonResponse(['result' => 'OK']);
    }
}
