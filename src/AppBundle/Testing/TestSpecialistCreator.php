<?php

namespace AppBundle\Testing;

use AppBundle\Entity\Skill;
use AppBundle\Entity\Specialization;
use AppBundle\Entity\User;
use AppBundle\Entity\UserSkills;
use AppBundle\Entity\UserSpecializations;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory as FakerFactory;

class TestSpecialistCreator
{
    const COUNTRIES = ['RU', 'US', 'GB', 'FR'];

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->faker = $faker = FakerFactory::create();
        $this->entityManager = $entityManager;
    }

    public function create(): User
    {
        $user = $this->createTestUser();
        $this->addSkills($user);
        $this->addSpecializations($user);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function createTestUser(): User
    {
        $user = new User();
        $date = new \DateTime();
        $email = $this->faker->email;
        $user->setFirstName($this->faker->firstName);
        $user->setLastName($this->faker->lastName);
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setCountry(self::COUNTRIES[array_rand(self::COUNTRIES)]);
        $user->setCity($this->faker->city);
        $user->setEnabled(true);
        $user->setPassword($this->faker->password);
        $user->setProfilePicture($this->faker->imageUrl());
        $user->setLikeToDo($this->faker->words(rand(5, 80), true));
        $user->setExpectation($this->faker->words(rand(5, 80), true));
        $user->setExperience($this->faker->words(rand(5, 80), true));
        $user->setAbout($this->faker->words(rand(5, 80), true));
        $user->setIsFake(true);
        $user->setCreatedAt($date);
        $this->entityManager->persist($user);

        return $user;
    }

    private function addSkills(User $user)
    {
        $skills = $this->entityManager->getRepository(Skill::class)->findAll();
        shuffle($skills);
        $skillsCount = rand(0, 10);

        for ($i = 0; $i < $skillsCount; ++$i) {
            $skill = array_pop($skills);
            $userSkill = new UserSkills();
            $userSkill->setSkill($skill);
            $userSkill->setUser($user);
            $userSkill->setPriority($i);
            $this->entityManager->persist($userSkill);
        }
        $this->entityManager->flush();
    }

    private function addSpecializations(User $user)
    {
        $specializations = $this->entityManager->getRepository(Specialization::class)->findAll();
        shuffle($specializations);
        $spCount = rand(1, 3);

        for ($i = 0; $i < $spCount; ++$i) {
            $sp = array_pop($specializations);
            $us = new UserSpecializations();
            $us->setUser($user);
            $us->setSpecialization($sp);
            $this->entityManager->persist($us);
        }
        $this->entityManager->flush();
    }
}
