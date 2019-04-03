<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\UserSubscriptions;
use App\Form\CommentType;
use App\Notifications\Notificator;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\UserSubscriptionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog/{page}", name="blog_list", defaults={"page": 1})
     *
     * @param ArticleRepository  $articleRepository
     * @param PaginatorInterface $paginator
     * @param int                $page
     *
     * @return Response
     */
    public function indexAction(
        ArticleRepository $articleRepository,
        PaginatorInterface $paginator,
        int $page = 1
    ) {
        $articles = $articleRepository->getPublishedQuery();

        $pagination = $paginator->paginate(
            $articles,
            $page
        );

        return $this->render('blog/list/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/blog/{article}/more", name="blog_more")
     *
     * @param Article                     $article
     * @param CommentRepository           $commentRepository
     * @param UserSubscriptionsRepository $subscriptionsRepository
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     */
    public function moreAction(
        Article $article,
        CommentRepository $commentRepository,
        UserSubscriptionsRepository $subscriptionsRepository)
    {
        /** @var User $user */
        $user = $this->getUser();

        $isUserSubscribed = $user
            ? (bool) $subscriptionsRepository->getUserSubscribedToArticleComments($this->getUser(), $article)
            : false;

        return $this->render('blog/more/index.html.twig', [
            'article' => $article,
            'commentForm' => $this->createForm(CommentType::class)->createView(),
            'comments' => $commentRepository->getForArticle($article),
            'isUserSubscribed' => $isUserSubscribed,
        ]);
    }

    /**
     * @Route("/blog/{article}/comment/submit", name="blog_add_comment")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     * @param Article                $article
     * @param Notificator            $notificator
     *
     * @return Response
     */
    public function submitComment(
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        Article $article,
        Notificator $notificator
    ) {
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $this->createComment($user, $form, $article, $em);
            $this->addFlash('add-comment-success', $translator->trans('comments.comment_successfully_added'));
            $notificator->newArticleComment($article, $comment);
        }

        return $this->redirectToRoute('blog_more', ['article' => $article->getId()]);
    }

    /**
     * @param User                   $user
     * @param FormInterface          $form
     * @param Article                $article
     * @param EntityManagerInterface $em
     *
     * @return Comment
     */
    private function createComment(User $user, FormInterface $form, Article $article, EntityManagerInterface $em): Comment
    {
        $comment = new Comment();
        $comment->setFrom($user);
        $comment->setEntity(Comment::ENTITY_ARTICLE);
        $comment->setToId($article->getId());
        $comment->setMessage($form->get('comment')->getData());
        $comment->setRemoved(false);
        $article->setCommentsCount($article->getCommentsCount() + 1);

        $em->persist($comment);
        $em->persist($article);
        $em->flush();

        return $comment;
    }

    /**
     * @Route("/blog/{article}/comments/subscribe", name="article_comment_subscription", methods={"POST"})
     *
     * @param Article                     $article
     * @param UserSubscriptionsRepository $subscriptionsRepository
     * @param EntityManagerInterface      $entityManager
     *
     * @return JsonResponse
     *
     * @throws NonUniqueResultException
     */
    public function commentsSubscribe(
        Article $article,
        UserSubscriptionsRepository $subscriptionsRepository,
        EntityManagerInterface $entityManager
    ) {
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException();
        }

        $subscription = $subscriptionsRepository->getUserSubscribedToArticleComments($user, $article);
        if (null !== $subscription) {
            $entityManager->remove($subscription);
            $entityManager->flush();

            return new JsonResponse(['success' => true]);
        }

        $subscription = new UserSubscriptions();
        $subscription->setUser($user);
        $subscription->setEntityId($article->getId());
        $subscription->setEvent(UserSubscriptions::EVENT_NEW_COMMENT_TO_ARTICLE_ADDED);
        $entityManager->persist($subscription);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
}
