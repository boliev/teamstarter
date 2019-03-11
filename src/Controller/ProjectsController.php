<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Offer;
use App\Entity\Project;
use App\Entity\SearchQueries;
use App\Entity\User;
use App\Form\ProposalToProjectType;
use App\Repository\OfferRepository;
use App\Repository\ProjectRepository;
use App\Search\ProjectSearcher\ProjectSearcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProjectsController extends AbstractController
{
    /**
     * @Route("/projects/{page}", name="projects_list", defaults={"page": 1})
     *
     * @param Request                  $request
     * @param EntityManagerInterface   $entityManager
     * @param ProjectSearcherInterface $projectSearcher
     * @param PaginatorInterface       $paginator
     * @param int                      $page
     *
     * @return Response
     */
    public function indexAction(
        Request $request,
        EntityManagerInterface $entityManager,
        ProjectSearcherInterface $projectSearcher,
        PaginatorInterface $paginator,
        int $page = 1
    ) {
        $searchQuery = $request->query->get('query');
        $isSearch = (null !== $searchQuery && '' !== $searchQuery);

        if ($isSearch) {
            $projects = $projectSearcher->search($searchQuery);
            $this->storeSearchQuery($entityManager, $searchQuery, \count($projects));
        } else {
            /** @var ProjectRepository $projectRepository */
            $projectRepository = $entityManager->getRepository(Project::class);
            $projects = $projectRepository->getPublishedQuery();
        }

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
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @param Project $project
     * @param OfferRepository $offerRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function submitProposal(
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        Project $project,
        OfferRepository $offerRepository
    ) {
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(ProposalToProjectType::class, null, ['project' => $project]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$user->isPro() && $offerRepository->getSentForLast24HoursCount($user) >= 10) {
                return $this->render('project/more/access_denied_add_proposal.html.twig', []);
            }
            $offer = $this->createOffer($user, $form, $project, $em);

            $this->addFlash('add-role-success', $translator->trans(
                'project.proposal_posted_success',
                    ['{offer}' => $this->generateUrl('dialogs_more', ['offer' => $offer->getId()])]
            ));

            $this->addFlash('do-not-show-proposal-already-message', 1);

            return $this->redirectToRoute('project_more', ['project' => $project->getId()]);
        }

        return $this->render('project/more/add_proposal.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param User                   $user
     * @param FormInterface          $form
     * @param Project                $project
     * @param EntityManagerInterface $em
     *
     * @throws \Exception
     *
     * @return Offer
     */
    private function createOffer(User $user, FormInterface $form, Project $project, EntityManagerInterface $em): Offer
    {
        $offer = new Offer();
        $offer->setFrom($user);
        $offer->setProject($project);
        $offer->setRole($form->get('role')->getData());
        $em->persist($offer);

        $message = new Message();
        $message->setFrom($user);
        $message->setTo($project->getUser());
        $message->setMessage($form->get('message')->getData());
        $message->setStatus(Message::STATUS_NEW);
        $message->setOffer($offer);
        $em->persist($message);

        $em->flush();

        return $offer;
    }
}
