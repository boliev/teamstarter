<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\Offer;
use AppBundle\Form\MessageType;
use AppBundle\Repository\OfferRepository;
use AppBundle\Sockets\Client;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;

class DialogsController extends Controller
{
    /**
     * @Route("/dialogs", name="dialogs_list")
     *
     * @param OfferRepository $offerRepository
     *
     * @return Response
     */
    public function indexAction(OfferRepository $offerRepository): Response
    {
        $user = $this->getUser();
        $dialogs = $offerRepository->getUserOffersSortedByLastMessage($user);

        return $this->render('dialogs/list/index.html.twig', [
            'dialogs' => $dialogs,
        ]);
    }

    /**
     * @Route("/dialogs/messages/{offer}", name="dialogs_more")
     *
     * @param Offer               $offer
     * @param TranslatorInterface $translator
     *
     * @return Response
     */
    public function moreAction(Offer $offer, TranslatorInterface $translator): Response
    {
        $user = $this->getUser();

        if (!$offer->isUserInvolved($user)) {
            throw new AccessDeniedException($translator->trans('dialogs.no_access'));
        }

        $addMessageForm = $this->createForm(MessageType::class);

        return $this->render('dialogs/more/index.html.twig', [
            'dialog' => $offer,
            'addMessageForm' => $addMessageForm->createView(),
            'websocketUrl' => $this->getParameter('websocket_url'),
            'websocketPort' => $this->getParameter('websocket_port'),
        ]);
    }

    /**
     * @Route("/dialogs/send", name="dialogs_send")
     *
     * @param Request                $request
     * @param OfferRepository        $offerRepository
     * @param TranslatorInterface    $translator
     * @param Client                 $client
     * @param EntityManagerInterface $entityManager
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @return JsonResponse
     */
    public function send(
        Request $request,
        OfferRepository $offerRepository,
        TranslatorInterface $translator,
        Client $client,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $addMessageForm = $this->createForm(MessageType::class);

        $addMessageForm->handleRequest($request);
        if ($addMessageForm->isSubmitted() && $addMessageForm->isValid()) {
            /** @var Offer $offer */
            $offer = $offerRepository->find($addMessageForm['offer']->getData());
            if (!$offer || !$offer->isUserInvolved($this->getUser())) {
                throw new NotFoundHttpException($translator->trans('dialogs.no_one_found'));
            }

            $message = $this->saveMessage($addMessageForm['message']->getData(), $offer, $entityManager);

            $client->sendMessage($message);
        }

        return new JsonResponse([]);
    }

    /**
     * @param string        $messageText
     * @param Offer         $offer
     * @param EntityManager $entityManager
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @return Message
     */
    private function saveMessage(string $messageText, Offer $offer, EntityManager $entityManager): Message
    {
        $message = new Message();
        $message->setMessage($messageText);
        $message->setFrom($this->getUser());
        $message->setOffer($offer);

        if ($offer->getFrom()->getId() !== $this->getUser()->getId()) {
            $message->setTo($offer->getFrom());
        } elseif ($offer->getProject()->getUser()->getId() !== $this->getUser()->getId()) {
            $message->setTo($offer->getProject()->getUser()->getId());
        } elseif ($offer->getTo()->getId() !== $this->getUser()->getId()) {
            $message->setTo($offer->getTo());
        }

        $entityManager->persist($message);
        $entityManager->flush();

        return $message;
    }
}
