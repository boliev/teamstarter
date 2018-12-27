<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Offer;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\OfferRepository;
use App\Sockets\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class DialogsController extends AbstractController
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
     * @param Offer $offer
     * @param string $websocketUrlForClient
     * @param string $websocketPortForClient
     * @param MessageRepository $messageRepository
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     *
     * @return Response
     */
    public function moreAction(
        Offer $offer,
        string $websocketUrlForClient,
        string $websocketPortForClient,
        MessageRepository $messageRepository,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator): Response
    {
        $user = $this->getUser();

        if (!$offer->isUserInvolved($user)) {
            throw new AccessDeniedException($translator->trans('dialogs.no_access'));
        }

        $addMessageForm = $this->createForm(MessageType::class);

        /** @var Message $message */
        foreach ($messageRepository->getNewOfferMessages($offer, $user) as $message) {
            $message->setStatus(Message::STATUS_READ);
            $entityManager->persist($message);
        }

        $entityManager->flush();

        return $this->render('dialogs/more/index.html.twig', [
            'dialog' => $offer,
            'addMessageForm' => $addMessageForm->createView(),
            'websocketUrl' => $websocketUrlForClient,
            'websocketPort' => $websocketPortForClient,
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
     * @param string                 $messageText
     * @param Offer                  $offer
     * @param EntityManagerInterface $entityManager
     *
     * @return Message
     */
    private function saveMessage(string $messageText, Offer $offer, EntityManagerInterface $entityManager): Message
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
