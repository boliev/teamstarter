<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class UserProfilerController extends AbstractController
{
    /**
     * @Route("/user/profile", name="user_profile_form")
     *
     * @param Request                      $request
     * @param EntityManagerInterface       $em
     * @param TranslatorInterface          $translator
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return Response
     */
    public function indexAction(Request $request, EntityManagerInterface $em, TranslatorInterface $translator, UserPasswordEncoderInterface $encoder)
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* @var UploadedFile $file */
            if ($form['newPassword']->getData()) {
                if (!$form['oldPassword']->getData()) {
                    $form->get('oldPassword')->addError(new FormError($translator->trans('user.old_password_required')));

                    return $this->renderProfileForm($form);
                }
                if ($form['newPassword']->getData() !== $form['newPasswordRepeat']->getData()) {
                    $form->get('newPasswordRepeat')->addError(new FormError($translator->trans('user.repeat_password_invalid')));

                    return $this->renderProfileForm($form);
                }
                if (!$encoder->isPasswordValid($user, $form['oldPassword']->getData())) {
                    $form->get('oldPassword')->addError(new FormError($translator->trans('user.old_password_invalid')));

                    return $this->renderProfileForm($form);
                }

                $user->setPassword($encoder->encodePassword($user, $form['newPassword']->getData()));
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('profile-success', $translator->trans('user.profile_form_success'));

            return $this->redirectToRoute($request->get('_route'));
        }

        return $this->renderProfileForm($form);
    }

    /**
     * @param FormInterface $form
     *
     * @return Response
     */
    private function renderProfileForm(FormInterface $form)
    {
        return $this->render('user/profile/index.html.twig', [
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
