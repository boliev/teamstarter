<?php

namespace AppBundle\Controller\API;

use FOS\UserBundle\Model\UserManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/api/v1/")
 */
class LoginController extends ApiController
{
    /**
     * @Route("try-to-login", name="api_v1_try_to_login")
     * @Method("POST")
     *
     * @param Request                 $request
     * @param UserManagerInterface    $userManager
     * @param EncoderFactoryInterface $encoderFactory
     * @param TranslatorInterface     $translator
     *
     * @return JsonResponse
     */
    public function indexAction(
        Request $request,
        UserManagerInterface $userManager,
        EncoderFactoryInterface $encoderFactory,
        TranslatorInterface $translator
    ) {
        $user = $userManager->findUserByUsername($request->request->get('_username'));
        if (!$user) {
            return $this->error($translator->trans('user.bad_credentials'));
        }

        $passwordEncoder = $encoderFactory->getEncoder($user);
        if (!$passwordEncoder->isPasswordValid($user->getPassword(), $request->request->get('_password'), $user->getSalt())) {
            return $this->error($translator->trans('user.bad_credentials'));
        }

        return new JsonResponse(['result' => 'OK']);
    }
}
