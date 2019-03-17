<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Message;
use App\Entity\Offer;
use App\Entity\Project;
use App\Entity\SearchQueries;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\ProposalToProjectType;
use App\Notifications\Notificator;
use App\Repository\CommentRepository;
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
     * @param OfferRepository   $offerRepository
     * @param CommentRepository $commentRepository
     * @param Project           $project
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function moreAction(
        OfferRepository $offerRepository,
        CommentRepository $commentRepository,
        Project $project
    ) {
        return $this->render('project/more/index.html.twig', [
            'project' => $project,
            'offer' => ($this->getUser() ? $offerRepository->getUserOfferForProject($this->getUser(), $project) : null),
            'commentForm' => $this->createForm(CommentType::class)->createView(),
            'comments' => $commentRepository->getForProject($project),
        ]);
    }

    /**
     * @Route("/projects/{project}/proposal/submit", name="project_add_proposal")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     * @param Project                $project
     * @param OfferRepository        $offerRepository
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
     * @Route("/projects/{project}/comment/submit", name="project_add_comment")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     * @param Project                $project
     * @param Notificator            $notificator
     *
     * @return Response
     */
    public function submitComment(
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        Project $project,
        Notificator $notificator
    ) {
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $this->createComment($user, $form, $project, $em);
            $this->addFlash('add-comment-success', $translator->trans('comments.comment_successfully_added'));
            $notificator->newProjectComment($project, $comment);
        }

        return $this->redirectToRoute('project_more', ['project' => $project->getId()]);
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

    /**
     * @param User                   $user
     * @param FormInterface          $form
     * @param Project                $project
     * @param EntityManagerInterface $em
     *
     * @return Comment
     */
    private function createComment(User $user, FormInterface $form, Project $project, EntityManagerInterface $em): Comment
    {
        $comment = new Comment();
        $comment->setFrom($user);
        $comment->setEntity(Comment::ENTITY_PROJECT);
        $comment->setToId($project->getId());
        $comment->setMessage($form->get('comment')->getData());
        $comment->setRemoved(false);
        $project->setCommentsCount($project->getCommentsCount() + 1);

        $em->persist($comment);
        $em->persist($project);
        $em->flush();

        return $comment;
    }
}
