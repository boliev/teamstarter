<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\Blog\ArticleType;
use App\Repository\ArticleRepository;
use App\Uploader\ArticleImageUploader;
use App\Uploader\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditorController extends AbstractController
{
    /**
     * @Route("/editor", name="editor_list")
     *
     * @param ArticleRepository $articleRepository
     *
     * @return Response
     */
    public function indexAction(ArticleRepository $articleRepository): Response
    {
    }

    /**
     * @Route("/editor/new", name="editor_new")
     *
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    public function newAction(EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $article->setStatus(Article::STATUS_DRAFT);
        $article->setAuthor($this->getUser());
        $entityManager->persist($article);
        $entityManager->flush();

        return $this->redirectToRoute('editor_edit', ['article' => $article->getId()]);
    }

    /**
     * @Route("/editor/{article}/edit", name="editor_edit")
     *
     * @param Article                $article
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    public function editAction(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $request->request->get('publish')) {
                $article->setStatus(Article::STATUS_PUBLISHED);
            }

            if (null !== $request->request->get('draft')) {
                $article->setStatus(Article::STATUS_DRAFT);
            }

            $entityManager->persist($article);
            $entityManager->flush();
        }

        return $this->render('editor/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    /**
     * @Route("/editor/{article}/upload-image/", name="editor_edit_upload_image")
     *
     * @param Article $article
     * @param EntityManagerInterface $em
     *
     * @param FileUploader $uploader
     * @return JsonResponse
     */
    public function uploadScreenAction(Article $article, EntityManagerInterface $em, ArticleImageUploader $uploader)
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($article->getAuthor()->getId() !== $user->getId()) {
            $this->redirectToRoute('homepage');
        }

        if (isset($_FILES['qqfile'])) {
            try {
                $file = $uploader->upload($article, $_FILES['qqfile']);
//                $projectScreen = new ProjectScreen();
//                $projectScreen->setProject($project);
//                $projectScreen->setScreenshot($file);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => $e->getMessage()], 400);
            }

//            $em->persist($projectScreen);
//            $em->flush();
//            $uploader->reModerateIfNeeded($project);

//            return new JsonResponse(['success' => true, 'picture' => $file.'?'.mt_rand(0, 5000), 'screenId' => $projectScreen->getId()]);
            return new JsonResponse(['success' => true, 'picture' => $file.'?'.mt_rand(0, 5000)]);
        }
    }
}
