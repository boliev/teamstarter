<?php

namespace App\Testing;

use App\Entity\Specialization;
use App\Entity\User;
use App\Entity\UserSkills;
use App\Entity\UserSpecializations;
use App\Repository\CountryRepository;
use App\Repository\SpecializationRepository;
use App\Repository\UserRepository;
use App\Service\SkillService;
use Doctrine\ORM\EntityManagerInterface;

class TestSpecialistCreator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TestProjectCreator
     */
    private $projectCreator;

    /**
     * @var string
     */
    private $dataDirectory;

    /**
     * @var string
     */
    private $kernelRoot;

    /**
     * @var SkillService
     */
    private $skillService;

    /**
     * @var SpecializationRepository
     */
    private $specializationRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CountryRepository
     */
    private $countryRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TestProjectCreator $projectCreator,
        SkillService $skillService,
        SpecializationRepository $specializationRepository,
        CountryRepository $countryRepository,
        UserRepository $userRepository,
        string $kernelRoot
    ) {
        $this->entityManager = $entityManager;
        $this->projectCreator = $projectCreator;
        $this->dataDirectory = $kernelRoot.'/Testing/data/';
        $this->kernelRoot = $kernelRoot;
        $this->skillService = $skillService;
        $this->specializationRepository = $specializationRepository;
        $this->userRepository = $userRepository;
        $this->countryRepository = $countryRepository;
    }

    /**
     * @param string $email
     *
     * @throws \Exception
     */
    public function create(string $email)
    {
        $specialists = json_decode(file_get_contents($this->dataDirectory.'specialists.json'), true);
        $created = false;
        foreach ($specialists as $specialist) {
            if ($email === $specialist['email']) {
                $user = $this->createTestUser($specialist);
                $this->entityManager->persist($user);
                $created = true;
            }
        }

        $this->entityManager->flush();

        if (false === $created) {
            throw new \Exception("There is no email {$email} in src/Testing/data/specialists.json");
        }
    }

    /**
     * @param array $userData
     *
     * @return User
     *
     * @throws \Exception
     */
    private function createTestUser(array $userData): User
    {
        $user = new User();
        $date = new \DateTime();
        $existedUser = $this->userRepository->findOneBy(['email' => $userData['email']]);
        if ($existedUser) {
            throw new \Exception("User {$userData['email']} already exists");
        }
        $country = $this->countryRepository->findOneBy(['code' => $userData['country']]);
        $user->setFirstName($userData['first_name']);
        $user->setLastName($userData['last_name']);
        $user->setUsername($userData['email']);
        $user->setEmail($userData['email']);
        $user->setCountry($country);
        $user->setCity($userData['city']);
        $user->setEnabled(true);
        $user->setLookingForProject($userData['looking_for_project']);
        $user->setLookingForPartner($userData['looking_for_partner'] ?? false);
        $user->setCanContributeHours($userData['canContributeHours'] ?? 5);
        $user->setPassword('Some123456#');
        $user->setLikeToDo($userData['like_to_do'] ?? null);
        $user->setExpectation($userData['expectation'] ?? null);
        $user->setExperience($userData['expereience'] ?? null);
        $user->setIsFake(true);
        $user->setCreatedAt($date);
        $this->entityManager->persist($user);
        if (isset($userData['avatar'])) {
            $this->setAvatar($user, $userData['avatar']);
        }

        if (isset($userData['skills'])) {
            $this->addSkills($user, $userData['skills']);
        }

        $this->addSpecializations($user, $userData['specialization']);
        if (isset($userData['projects']) && count($userData['projects']) > 0) {
            foreach ($userData['projects'] as $projectData) {
                $this->projectCreator->create($user, $projectData);
            }
        }

        return $user;
    }

    private function setAvatar(User $user, string $avatar)
    {
        $avatarFile = $this->dataDirectory.$avatar;
        $avatarDir = $this->kernelRoot.'/../public/avatars/fk/';
        $newAvatar = '/avatars/fk/'.$user->getId().'jpg';
        if (!is_dir($avatarDir)) {
            mkdir($avatarDir);
        }
        copy($avatarFile, $avatarDir.$user->getId().'jpg');
        $user->setProfilePicture($newAvatar);
        $this->entityManager->persist($user);
    }

    private function addSkills(User $user, array $skills)
    {
        $priority = 1;
        foreach ($skills as $skill) {
            $skillEntity = $this->skillService->getOrCreateSkill($skill);
            $userSkill = new UserSkills();
            $userSkill->setSkill($skillEntity);
            $userSkill->setUser($user);
            $userSkill->setPriority($priority);
            $this->entityManager->persist($userSkill);
            ++$priority;
        }
        $this->entityManager->flush();
    }

    private function addSpecializations(User $user, array $specializations)
    {
        foreach ($specializations as $specialization) {
            /** @var Specialization $specializationEntity */
            $specializationEntity = $this->specializationRepository->find($specialization);
            $us = new UserSpecializations();
            $us->setUser($user);
            $us->setSpecialization($specializationEntity);
            $this->entityManager->persist($us);
        }
        $this->entityManager->flush();
    }
}
