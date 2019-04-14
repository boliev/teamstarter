<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Message;
use App\Entity\Offer;
use App\Entity\Project;
use App\Entity\SearchQueries;
use App\Entity\User;
use App\Entity\UserSubscriptions;
use App\Form\CommentType;
use App\Form\ProposalToProjectType;
use App\Notifications\Notificator;
use App\Repository\CommentRepository;
use App\Repository\OfferRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserSubscriptionsRepository;
use App\Search\ProjectSearcher\ProjectSearcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProjectsController extends AbstractController
{
    /**
     * @Route("/projects/{page}", name="projects_list", defaults={"page": 1})
     *
     * @param Request                     $request
     * @param EntityManagerInterface      $entityManager
     * @param ProjectSearcherInterface    $projectSearcher
     * @param PaginatorInterface          $paginator
     * @param UserSubscriptionsRepository $userSubscriptionsRepository
     * @param int                         $page
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     */
    public function indexAction(
        Request $request,
        EntityManagerInterface $entityManager,
        ProjectSearcherInterface $projectSearcher,
        PaginatorInterface $paginator,
        UserSubscriptionsRepository $userSubscriptionsRepository,
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

        $isUserSubscribedToDigest = false;
        if ($this->getUser()) {
            $isUserSubscribedToDigest = $userSubscriptionsRepository->isUserSubscribedToDigest($this->getUser());
        }

        return $this->render('project/list/index.html.twig', [
            'pagination' => $pagination,
            'searchQuery' => $searchQuery,
            'isUserSubscribedToDigest' => $isUserSubscribedToDigest,
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
     * @param OfferRepository             $offerRepository
     * @param CommentRepository           $commentRepository
     * @param UserSubscriptionsRepository $subscriptionsRepository
     * @param Project                     $project
     * @param EntityManagerInterface      $entityManager
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     */
    public function moreAction(
        OfferRepository $offerRepository,
        CommentRepository $commentRepository,
        UserSubscriptionsRepository $subscriptionsRepository,
        Project $project,
        EntityManagerInterface $entityManager
    ) {
        /** @var User $user */
        $user = $this->getUser();

        $project->setViewsCount($project->getViewsCount() + 1);
        $entityManager->persist($project);
        $entityManager->flush();

        $isUserSubscribed = $user
            ? (bool) $subscriptionsRepository->getUserSubscribedToProjectComments($this->getUser(), $project)
            : false;

        $userOfferForThisProject = $user && $user->getId() !== $project->getUser()->getId()
            ? $offerRepository->getUserOfferForProject($this->getUser(), $project)
            : null;

        return $this->render('project/more/index.html.twig', [
            'project' => $project,
            'offer' => $userOfferForThisProject,
            'commentForm' => $this->createForm(CommentType::class)->createView(),
            'comments' => $commentRepository->getForProject($project),
            'isUserSubscribed' => $isUserSubscribed,
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
     * @throws NonUniqueResultException
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
     * @Route("/projects/{project}/comments/subscribe", name="project_comment_subscription", methods={"POST"})
     *
     * @param Project                     $project
     * @param UserSubscriptionsRepository $subscriptionsRepository
     * @param EntityManagerInterface      $entityManager
     *
     * @return JsonResponse
     *
     * @throws NonUniqueResultException
     */
    public function commentsSubscribe(
        Project $project,
        UserSubscriptionsRepository $subscriptionsRepository,
        EntityManagerInterface $entityManager
    ) {
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException();
        }

        $subscription = $subscriptionsRepository->getUserSubscribedToProjectComments($user, $project);
        if (null !== $subscription) {
            $entityManager->remove($subscription);
            $entityManager->flush();

            return new JsonResponse(['success' => true]);
        }

        $subscription = new UserSubscriptions();
        $subscription->setUser($user);
        $subscription->setEntityId($project->getId());
        $subscription->setEvent(UserSubscriptions::EVENT_NEW_COMMENT_TO_PROJECT_ADDED);
        $entityManager->persist($subscription);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
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
