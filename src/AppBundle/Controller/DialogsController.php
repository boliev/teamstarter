<?php

namespace AppBundle\Controller;

use AppBundle\Repository\OfferRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DialogsController extends Controller
{
    /**
     * @Route("/dialogs", name="dialogs_list")
     *
     * @param OfferRepository $offerRepository
     *
     * @return Response
     */
    public function indexAction(OfferRepository $offerRepository)
    {
        $user = $this->getUser();
        $dialogs = $offerRepository->getUserOffersSortedByLastMessage($user);

        return $this->render('dialogs/list/index.html.twig', [
            'dialogs' => $dialogs,
        ]);
    }
}
