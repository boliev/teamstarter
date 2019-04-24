<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     *
     * @param ProjectRepository $projectRepository
     * @param PaginatorInterface $paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(
        ProjectRepository $projectRepository,
        PaginatorInterface $paginator
    )
    {
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'projects' => $pagination = $paginator->paginate($projectRepository->getPublishedOrderedByIdQuery(), 1, 6)
        ]);
    }

    /**
     * @Route("/terms", name="terms")
     *
     * @param Request $request
     * @param string $locale
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function termsAction(Request $request, string $locale, \Parsedown $parsedown)
    {
        $terms = $this->renderView('texts/'.$locale.'/terms.html.twig', [
            'domain' => $request->getHost(),
        ]);

        $terms = $parsedown->text($terms);

        return $this->render('default/terms.html.twig', [
            'domain' => $request->getHost(),
            'terms' => $terms
        ]);
    }

    /**
     * @Route("/achievements", name="achievements_page")
     *
     * @param Request $request
     * @param string $locale
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function achievementsAction(Request $request, string $locale, \Parsedown $parsedown)
    {
        $terms = $this->renderView('texts/'.$locale.'/terms.html.twig', [
            'domain' => $request->getHost(),
        ]);

        $terms = $parsedown->text($terms);

        return $this->render('default/terms.html.twig', [
            'domain' => $request->getHost(),
            'terms' => $terms
        ]);
    }

}
