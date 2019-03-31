<?php

namespace App\Controller;

use App\Billing\Exception\BillingException;
use App\Billing\PaymentProcessor;
use App\Entity\Article;
use App\Entity\Project;
use App\Notifications\Notificator;
use App\Repository\ArticleRepository;
use App\Repository\ProjectRepository;
use App\Search\ProjectSearcher\ProjectSearcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog_list")
     *
     * @param ArticleRepository $articleRepository
     * @param PaginatorInterface $paginator
     * @param int $page
     * @return Response
     *
     */
    public function indexAction(
        ArticleRepository $articleRepository,
        PaginatorInterface $paginator,
        int $page = 1
    ) {
        /** @var ProjectRepository $projectRepository */
        $articles = $articleRepository->getPublishedQuery();

        $pagination = $paginator->paginate(
            $articles,
            $page
        );

        return $this->render('blog/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/blog/{article}/more", name="blog_more")
     *
     * @return Response
     *
     */
    public function moreAction(Article $article) {

    }
}
