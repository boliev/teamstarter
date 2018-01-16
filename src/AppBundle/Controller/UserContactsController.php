<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Entity\User;
use AppBundle\Entity\UserContacts;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Translator;

class UserContactsController extends AbstractController
{
    /**
     * @Route("/user/contacts", name="user_contacts_form")
     * @Route("/specify/contacts", name="specify_contacts_form")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param Translator             $translator
     *
     * @return Response
     */
    public function indexAction(Request $request, EntityManagerInterface $em, Translator $translator)
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($request->isMethod(Request::METHOD_POST)) {
            $this->removeAllUserContacts($user, $em);

            $contacts = $request->request->get('contacts');
            foreach ($contacts as $type => $contactForm) {
                $this->addUserContact($user, $contactForm, $type, $em, $translator);
            }
            $em->flush();

            if ('specify_contacts_form' === $request->get('_route')) {
                return $this->redirectToRoute('homepage');
            } else {
                return $this->redirectToRoute($request->get('_route'));
            }
        }

        $userContactsForView = $this->getUserContactsForView($user, $em);

        return $this->render('user/contacts/index.html.twig', [
            'contacts' => $em->getRepository(Contact::class)->findAll(),
            'userContacts' => $userContactsForView,
            'contactsIsClear' => (bool) !(count($userContactsForView)),
        ]);
    }

    /**
     * @param User                   $user
     * @param EntityManagerInterface $em
     */
    private function removeAllUserContacts(User $user, EntityManagerInterface $em)
    {
        $userContacts = $em->getRepository(UserContacts::class)->findBy(['user' => $user]);

        foreach ($userContacts as $userContact) {
            $em->remove($userContact);
        }
        $em->flush();
    }

    /**
     * @param User                   $user
     * @param array                  $contactForm
     * @param int                    $type
     * @param EntityManagerInterface $em
     * @param Translator             $translator
     *
     * @return UserContacts|null
     */
    private function addUserContact(User $user, array $contactForm, int $type, EntityManagerInterface $em, Translator $translator): ?UserContacts
    {
        if (!isset($contactForm['enabled'])) {
            return null;
        }

        $contact = $em->getRepository(Contact::class)->find($type);
        if (!$contact) {
            throw new BadRequestHttpException($translator->trans('contacts.no_contact', $type));
        }
        $userContact = new UserContacts();
        $userContact->setUser($user);
        $userContact->setContact($contact);
        $userContact->setVisible(isset($contactForm['visible']));
        $userContact->setPrefered(isset($contactForm['prefered']));
        if (isset($contactForm['additional'])) {
            $userContact->setAdditional(trim($contactForm['additional']));
        }
        if (isset($contactForm['number'])) {
            $userContact->setNumber(trim($contactForm['number']));
        }
        $em->persist($userContact);

        return $userContact;
    }

    /**
     * @param User                   $user
     * @param EntityManagerInterface $em
     *
     * @return array
     */
    private function getUserContactsForView(User $user, EntityManagerInterface $em): array
    {
        $userContacts = $em->getRepository(UserContacts::class)->findBy(['user' => $user]);
        $userContactsForView = [];
        foreach ($userContacts as $userContact) {
            $userContactsForView[$userContact->getContact()->getId()] = $userContact;
        }

        return $userContactsForView;
    }
}
