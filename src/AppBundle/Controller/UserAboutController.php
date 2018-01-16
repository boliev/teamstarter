<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserAboutType;
use AppBundle\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserAboutController extends AbstractController
{
    /**
     * @Route("/user/about", name="user_about_form")
     * @Route("/specify/about", name="specify_about_form")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function indexAction(Request $request, EntityManagerInterface $em, UserService $userService)
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
}
