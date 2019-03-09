<?php

namespace App\Controller;

use App\Entity\BetaRequest;
use App\Entity\User;
use App\Form\UserPromoCodeType;
use App\Notifications\Notificator;
use App\Repository\PromoCodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserPromoCodeController extends AbstractController
{
    /**
     * @Route("/user/promo-code", name="user_promo_code_form")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param PromoCodeRepository    $promoCodeRepository
     * @param TranslatorInterface    $translator
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function indexAction(
        Request $request,
        EntityManagerInterface $em,
        PromoCodeRepository $promoCodeRepository,
        TranslatorInterface $translator
    ) {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserPromoCodeType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $promoCodeString = $form['promoCode']->getData();
            $promoCode = $promoCodeRepository->findByCode($promoCodeString);
            if ($promoCode) {
                $user->setPromoCode($promoCode);
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('homepage');
            } else {
                $form->get('promoCode')->addError(new FormError($translator->trans('registration.promo_code_error')));
            }
        }

        return $this->render('user/promocode/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/promo-code/sign-up", name="user_promo_code_sign_up")
     *
     * @param EntityManagerInterface $em
     * @param Notificator            $notificator
     *
     * @return Response
     */
    public function signUpForBeta(
        EntityManagerInterface $em,
        Notificator $notificator
    ) {
        try {
            $betaRequest = new BetaRequest();
            $betaRequest->setUser($this->getUser());
            $em->persist($betaRequest);
            $em->flush();
        } catch (\Exception $e) {
        }

        $notificator->newPromoCodeRequest();

        return $this->render('user/promocode/request.html.twig', []);
    }
}
