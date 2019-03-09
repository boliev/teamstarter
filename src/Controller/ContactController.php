<?php

namespace App\Controller;

use App\Entity\SupportRequest;
use App\Form\ContactType;
use App\Notifications\Notificator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact_form")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param Notificator            $notificator
     *
     * @return Response
     */
    public function indexAction(
        Request $request,
        EntityManagerInterface $em,
        Notificator $notificator
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
            $notificator->newContactFormRequest($supportRequest);

            return $this->render('contact/index/success.html.twig', []);
        }

        return $this->render('contact/index/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
