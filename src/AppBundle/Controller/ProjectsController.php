<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Offer;
use AppBundle\Entity\Project;
use AppBundle\Entity\SearchQueries;
use AppBundle\Entity\User;
use AppBundle\Form\ProposalToProjectType;
use AppBundle\Repository\OfferRepository;
use AppBundle\Search\ProjectSearcher\ProjectSearcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

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
        $searchQuery = $request->query->get('query');
        $isSearch = (null !== $searchQuery && '' !== $searchQuery);
        if ($isSearch) {
            $projects = $projectSearcher->search($searchQuery);
            $this->storeSearchQuery($entityManager, $searchQuery, count($projects));
        } else {
            $projectRepository = $entityManager->getRepository(Project::class);
            $projects = $projectRepository->getPublishedQuery();
        }

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

    /**
     * @param EntityManagerInterface $entityManager
     * @param string                 $searchQuery
     * @param int                    $count
     */
    private function storeSearchQuery(EntityManagerInterface $entityManager, string $searchQuery, int $count): void
    {
        $searchQueries = new SearchQueries();
        $searchQueries->setQuery($searchQuery);
        $searchQueries->setCount($count);
        $searchQueries->setType(SearchQueries::TYPE_PROJECTS);
        $entityManager->persist($searchQueries);
        $entityManager->flush();
    }

    /**
     * @Route("/projects/{project}/more", name="project_more")
     *
     * @param Project         $project
     * @param OfferRepository $offerRepository
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return Response
     */
    public function moreAction(
        OfferRepository $offerRepository,
        Project $project
    ) {
        return $this->render('project/more/index.html.twig', [
            'project' => $project,
            'offer' => ($this->getUser() ? $offerRepository->getUserOfferForProject($this->getUser(), $project) : null),
        ]);
    }

    /**
     * @Route("/projects/{project}/proposal/submit", name="project_add_proposal")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     * @param Project                $project
     *
     * @return Response
     */
    public function submitProposal(Request $request, EntityManagerInterface $em, TranslatorInterface $translator, Project $project)
    {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(ProposalToProjectType::class, null, ['project' => $project]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->createOffer($user, $form, $project, $em);

            $this->addFlash('add-role-success', $translator->trans('project.proposal_posted_success'));
            $this->addFlash('do-not-show-proposal-already-message', 1);

            return $this->redirectToRoute('project_more', ['project' => $project->getId()]);
        }

        return $this->render('project/more/add_proposal.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    private function createOffer(User $user, FormInterface $form, Project $project, EntityManagerInterface $em): void
    {
        $offer = new Offer();
        $offer->setFrom($user);
        $offer->setMessage($form->get('message')->getData());
        $offer->setProject($project);
        $offer->setRole($form->get('role')->getData());
        $em->persist($offer);
        $em->flush();
    }
}
