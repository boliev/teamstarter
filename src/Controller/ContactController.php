<?php

namespace App\Controller;

use App\Entity\SupportRequest;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact_form")
     *
     * @param string $fromEmailAddress
     * @param string $fromName
     * @param string $supportEmail
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param \Swift_Mailer $mailer
     * @param TranslatorInterface $translator
     *
     * @return Response
     * @throws \Exception
     */
    public function indexAction(
        string $fromEmailAddress,
        string $fromName,
        string $supportEmail,
        Request $request,
        EntityManagerInterface $em,
        \Swift_Mailer $mailer,
        TranslatorInterface $translator
    ) {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SupportRequest $supportRequest */
            $supportRequest = $form->getData();
            if ($this->getUser()) {
                $supportRequest->setUser($this->getUser());
            }
            $em->persist($supportRequest);
            $em->flush();

            $message = (new \Swift_Message($translator->trans('contact.support_email.subject')))
                ->setFrom($fromEmailAddress, $fromName)
                ->setTo($supportEmail)
                ->setBody($translator->trans('contact.support_email.message', [
                    '%id%' => $supportRequest->getId(),
                    '%email%' => $supportRequest->getEmail(),
                    '%name%' => $supportRequest->getUser() ? $supportRequest->getUser()->getFullName() : '-',
                    '%user_id%' => $supportRequest->getUser() ? $supportRequest->getUser()->getId() : '-',
                    '%user_pro%' => $supportRequest->getUser() && $supportRequest->getUser()->isPro() ? '+' : '-',
                    '%title%' => $supportRequest->getTitle(),
                    '%text%' => $supportRequest->getDescription(),
                ]), 'text/html');

            $mailer->send($message);

            return $this->render('contact/index/success.html.twig', []);
        }

        return $this->render('contact/index/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
