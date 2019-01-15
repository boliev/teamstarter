<?php

namespace App\Controller;

use App\Entity\Specialization;
use App\Entity\User;
use App\Entity\UserSkills;
use App\Entity\UserSpecializations;
use App\Service\SkillService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserSpecializationController extends AbstractController
{
    /**
     * @Route("/user/specialization", name="user_specialization_form")
     * @Route("/specify/specialization", name="specify_specialization_form")
     *
     * @param Request                $request
     * @param SkillService           $skillService
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     *
     * @return Response
     */
    public function indexAction(Request $request, SkillService $skillService, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $user = $this->getUser();

        if ($request->isMethod(Request::METHOD_POST)) {
            $specs = $request->request->get('specializations');
            try {
                $this->checkSpecializations($specs, $translator);
            } catch (\Exception $e) {
                $this->addFlash('specialization-errors', $e->getMessage());

                return $this->redirectToRoute($request->get('_route'));
            }
            $this->saveSpecializations($specs, $user, $em, $translator);
            $em->flush();

            $skills = explode(',', $request->request->get('skills'));
            $this->saveSkills($skills, $user, $skillService, $em);

            if ('specify_specialization_form' === $request->get('_route')) {
                return $this->redirectToRoute('homepage');
            } else {
                $this->addFlash('specialization-success', $translator->trans('specialization.form_success'));

                return $this->redirectToRoute($request->get('_route'));
            }
        }

        $userSkills = $em->getRepository(UserSkills::class)->findBy(['user' => $user]);
        usort($userSkills, function (UserSkills $a, UserSkills $b) {
            return $a->getPriority() <=> $b->getPriority();
        });
        $userSpecializations = $em->getRepository(UserSpecializations::class)->findBy(['user' => $user]);
        $us = [];
        foreach ($userSpecializations as $userSpecialization) {
            $us[$userSpecialization->getSpecialization()->getId()] = $userSpecialization;
        }

        return $this->render('user/specialization/index.html.twig', [
            'specializations' => $em->getRepository(Specialization::class)->findAll(),
            'userSkills' => $userSkills,
            'userSpecialization' => $us,
        ]);
    }

    /**
     * @param array|null          $specs
     * @param TranslatorInterface $translator
     */
    private function checkSpecializations(array $specs = null, TranslatorInterface $translator): void
    {
        if (!$specs) {
            throw new BadRequestHttpException($translator->trans('specialization.no_specializations_selected'));
        }

        if (count($specs) > UserSpecializations::MAX_FOR_USER) {
            throw new BadRequestHttpException($translator->trans('specialization.too_many_specializations_selected'));
        }
    }

    /**
     * @param array                  $specs
     * @param User                   $user
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     */
    private function saveSpecializations(array $specs, User $user, EntityManagerInterface $em, TranslatorInterface $translator): void
    {
        $userSpecializations = $em->getRepository(UserSpecializations::class)->findBy(['user' => $user]);
        foreach ($userSpecializations as $userSpecialization) {
            $em->remove($userSpecialization);
        }
        $em->flush();

        foreach ($specs as $spec) {
            $specialization = $em->getRepository(Specialization::class)->find($spec);
            if (!$specialization) {
                throw new BadRequestHttpException($translator->trans('specialization.cant_find_specialization'));
            }

            $userSpecialization = new UserSpecializations();
            $userSpecialization->setUser($user);
            $userSpecialization->setSpecialization($specialization);
            $em->persist($userSpecialization);
        }
    }

    /**
     * @param array                  $skills
     * @param User                   $user
     * @param SkillService           $skillService
     * @param EntityManagerInterface $em
     */
    private function saveSkills(array $skills, User $user, SkillService $skillService, EntityManagerInterface $em): void
    {
        $skillService->cleanSkillsForUser($user);
        foreach ($skills as $priority => $skill) {
            $skillEntity = $skillService->getOrCreateSkill($skill);
            $userSkill = new UserSkills();
            $userSkill->setUser($user);
            $userSkill->setSkill($skillEntity);
            $userSkill->setPriority($priority);
            $em->persist($userSkill);
            $em->flush();
        }
    }
}
