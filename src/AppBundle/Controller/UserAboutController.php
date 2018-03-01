<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserAboutType;
use AppBundle\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

class UserAboutController extends AbstractController
{
    /**
     * @Route("/user/about", name="user_about_form")
     * @Route("/specify/about", name="specify_about_form")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param UserService            $userService
     * @param TranslatorInterface    $translator
     *
     * @return Response
     */
    public function indexAction(Request $request, EntityManagerInterface $em, UserService $userService, TranslatorInterface $translator)
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserAboutType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* @var UploadedFile $file */
            if ($form['profilePicture']->getData()) {
                try {
                    $file = $userService->uploadAvatar($user, $form['profilePicture']->getData());
                    $user->setProfilePicture($file);
                } catch (\Exception $e) {
                    $form->get('profilePicture')->addError(new FormError($e->getMessage()));
                }
            }

            $em->persist($user);
            $em->flush();

            if ('specify_about_form' === $request->get('_route')) {
                return $this->redirectToRoute('homepage');
            } else {
                $this->addFlash('about-success', $translator->trans('user.about_form_success'));

                return $this->redirectToRoute($request->get('_route'));
            }
        }

        return $this->render('user/about/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/specify/about/skip", name="specify_about_form_skip")
     *
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function skipAboutFormAction(EntityManagerInterface $em)
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->setAboutFormSkipped(new \DateTime());
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/specify/about/upload-profile", name="specify_about_upload_avatar")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param UserService            $userService
     * @param TranslatorInterface    $translator
     *
     * @return Response
     */
    public function uploadProfileAction(Request $request, EntityManagerInterface $em, UserService $userService, TranslatorInterface $translator)
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($request->request->get('qqfilename')) {
            try {
                $uploadedFile = new UploadedFile($_FILES['qqfile']['tmp_name'], $_FILES['qqfile']['name'], mime_content_type($_FILES['qqfile']['tmp_name']));
                $file = $userService->uploadAvatar($user, $uploadedFile);
                $user->setProfilePicture($file);
            } catch (\Exception $e) {
//                    $form->get('profilePicture')->addError(new FormError($e->getMessage()));
                return new JsonResponse($e->getMessage(), 400);
            }

            $em->persist($user);
            $em->flush();

            return new JsonResponse(['success' => true, 'picture' => $user->getProfilePicture()]);
        }

//        return $this->render('user/about/index.html.twig', [
//            'form' => $form->createView(),
//        ]);
    }
}
