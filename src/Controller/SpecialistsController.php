<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Offer;
use App\Entity\SearchQueries;
use App\Entity\User;
use App\Form\OfferToSpecialistType;
use App\Repository\OfferRepository;
use App\Search\SpecialistSearcher\SpecialistSearcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SpecialistsController extends AbstractController
{
    /**
     * @Route("/specialists/{page}", name="specialists_list", defaults={"page": 1})
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SpecialistSearcherInterface $specialistSearcher
     * @param PaginatorInterface $paginator
     * @param int $page
     *
     * @return Response
     */
    public function indexAction(
        Request $request,
        EntityManagerInterface $entityManager,
        SpecialistSearcherInterface $specialistSearcher,
        PaginatorInterface $paginator,
        int $page = 1
    ) {
        $ids = null;
        $searchQuery = $request->query->get('query');
        $isSearch = (null !== $searchQuery && '' !== $searchQuery);
        if ($isSearch) {
            $specialists = $specialistSearcher->search($searchQuery);
            $this->storeSearchQuery($entityManager, $searchQuery, count($specialists));
        } else {
            $userRepository = $entityManager->getRepository(User::class);
            $specialists = $userRepository->getAvailableSpecialists();
        }

        $pagination = $paginator->paginate(
            $specialists,
            $page
        );

        return $this->render('specialists/list/index.html.twig', [
            'pagination' => $pagination,
            'searchQuery' => $searchQuery,
        ]);
    }

    /**
     * @Route("/specialists/{user}/more", name="specialists_more")
     *
     * @param User                $user
     * @param TranslatorInterface $translator
     * @param OfferRepository     $offerRepository
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return Response
     */
    public function moreAction(
        User $user,
        TranslatorInterface $translator,
        OfferRepository $offerRepository
    ) {
        if (!$user->isSpecialist()) {
            // not_found
            throw new NotFoundHttpException($translator->trans('specialists.not_found'));
        }

        return $this->render('specialists/more/index.html.twig', [
            'user' => $user,
            'offer' => ($this->getUser() ? $offerRepository->getUserOfferForSpecialist($this->getUser(), $user) : null),
        ]);
    }

    /**
     * @Route("/specialists/{specialist}/offer/submit", name="specialist_add_offer")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     * @param User                   $specialist
     *
     * @return Response
     */
    public function submitOffer(Request $request, EntityManagerInterface $em, TranslatorInterface $translator, User $specialist)
    {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(OfferToSpecialistType::class, null, ['user' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->createOffer($user, $form, $specialist, $em);

            $this->addFlash('add-offer-success', $translator->trans('project.offer_posted_success'));
            $this->addFlash('do-not-show-offer-already-message', 1);

            return $this->redirectToRoute('specialists_more', ['user' => $specialist->getId()]);
        }

        return $this->render('specialists/more/add_offer.html.twig', [
            'specialist' => $specialist,
            'form' => $form->createView(),
        ]);
    }

    private function storeSearchQuery(EntityManagerInterface $entityManager, string $searchQuery, int $count): void
    {
        $searchQueries = new SearchQueries();
        $searchQueries->setQuery($searchQuery);
        $searchQueries->setCount($count);
        $searchQueries->setType(SearchQueries::TYPE_SPECIALISTS);
        $entityManager->persist($searchQueries);
        $entityManager->flush();
    }

    private function createOffer(User $fromUser, FormInterface $form, User $specialist, EntityManagerInterface $em): void
    {
        $offer = new Offer();
        $offer->setFrom($fromUser);
        $offer->setProject($form->get('project')->getData());
        $offer->setTo($specialist);
        $offer->setRole($form->get('role')->getData());
        $em->persist($offer);

        $message = new Message();
        $message->setFrom($fromUser);
        $message->setTo($specialist);
        $message->setMessage($form->get('message')->getData());
        $message->setStatus(Message::STATUS_NEW);
        $message->setOffer($offer);
        $em->persist($message);

        $em->flush();
    }
}
