<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SearchQueries;
use AppBundle\Entity\User;
use AppBundle\Search\SpecialistSearcher\SpecialistSearcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpecialistsController extends Controller
{
    /**
     * @Route("/specialists/{page}", name="specialists_list", defaults={"page": 1})
     *
     * @param Request                     $request
     * @param EntityManagerInterface      $entityManager
     * @param SpecialistSearcherInterface $specialistSearcher
     * @param int                         $page
     *
     * @return Response
     */
    public function indexAction(
        Request $request,
        EntityManagerInterface $entityManager,
        SpecialistSearcherInterface $specialistSearcher,
        int $page = 1
    ) {
        $ids = null;
        $searchQuery = $request->query->get('query');
        $isSearch = (null !== $searchQuery && '' !== $searchQuery);
        if ($isSearch) {
            $specialists = $specialistSearcher->search($searchQuery);
            $this->storeSearchQuery($entityManager, $searchQuery, count($specialists));
        } else {
            $userRepository = $entityManager->getRepository(User::class);
            $specialists = $userRepository->getAvailableSpecialists();
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $specialists,
            $page
        );

        return $this->render('specialists/list/index.html.twig', [
            'pagination' => $pagination,
            'searchQuery' => $searchQuery,
        ]);
    }

    private function storeSearchQuery(EntityManagerInterface $entityManager, string $searchQuery, int $count): void
    {
        $searchQueries = new SearchQueries();
        $searchQueries->setQuery($searchQuery);
        $searchQueries->setCount($count);
        $searchQueries->setType(SearchQueries::TYPE_SPECIALISTS);
        $entityManager->persist($searchQueries);
        $entityManager->flush();
    }
}
