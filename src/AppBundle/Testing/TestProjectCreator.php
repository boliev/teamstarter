<?php

namespace AppBundle\Testing;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectOpenRole;
use AppBundle\Entity\ProjectOpenRoleSkills;
use AppBundle\Entity\ProjectScreen;
use AppBundle\Entity\Skill;
use AppBundle\Entity\Specialization;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory as FakerFactory;

class TestProjectCreator
{
    const COUNTRIES = ['RU', 'US', 'GB', 'FR'];

    const PROGRESS_STATUSES = [
        Project::STATUS_PUBLISHED,
        Project::STATUS_CLOSED,
        Project::STATUS_DECLINED,
        Project::STATUS_INREVIW,
        Project::STATUS_UNFINISHED,
    ];

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * TestProjectCreator constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->faker = $faker = FakerFactory::create();
        $this->entityManager = $entityManager;
    }

    /**
     * @param User        $user
     * @param null|string $progressStatus
     *
     * @return Project
     */
    public function create(User $user, ?string $progressStatus)
    {
        $project = $this->createTestProject($user, $progressStatus);
        $rolesCount = rand(0, 10);
        for ($i = 0; $i < $rolesCount; ++$i) {
            $this->createRole($project);
        }

        $screensCount = rand(0, 10);
        for ($i = 0; $i < $screensCount; ++$i) {
            $this->createScreen($project);
        }

        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return $project;
    }

    /**
     * @param User        $user
     * @param null|string $progressStatus
     *
     * @return Project
     */
    private function createTestProject(User $user, ?string $progressStatus): Project
    {
        $projectStatuses = $this->entityManager->getRepository('AppBundle:ProjectStatus')->findAll();
        shuffle($projectStatuses);
        $project = new Project();
        $project->setUser($user);
        $date = new \DateTime();
        $project->setName(sprintf('%s %s', $this->faker->words(rand(3, 7), true), $date->format('Y-m-d h:i')));
        $project->setStatus($projectStatuses[0]);
        $project->setMission($this->faker->words(rand(5, 35), true));
        $project->setDescription($this->faker->text(rand(100, 1000)));
        $project->setCountry(self::COUNTRIES[array_rand(self::COUNTRIES)]);
        $project->setCity($this->faker->city);
        if ($progressStatus && in_array($progressStatus, self::PROGRESS_STATUSES)) {
            $project->setProgressStatus($progressStatus);
        } else {
            $project->setProgressStatus(self::PROGRESS_STATUSES[array_rand(self::PROGRESS_STATUSES)]);
        }
        $random_day = rand(0, 365);
        $date = new \DateTime();
        $date = $date->modify(sprintf('-%d days', $random_day));
        $project->setCreatedAt($date);
        $this->entityManager->persist($project);

        return $project;
    }

    /**
     * @param Project $project
     *
     * @return ProjectOpenRole
     */
    private function createRole(Project $project)
    {
        $specializations = $this->entityManager->getRepository(Specialization::class)->findAll();
        $skills = $this->entityManager->getRepository(Skill::class)->findAll();
        shuffle($skills);
        /** @var Specialization $specialization */
        $specialization = $specializations[array_rand($specializations)];
        $role = new ProjectOpenRole();
        $roleNamesBegins = ['Need', 'Wanted', 'I wanna the best', 'Give me', 'Where is my', 'Ninja', 'Super-puper'];
        $roleNamesEndings = ['fast', 'please', 'for work', 'for slavery', 'for free', '!'];
        shuffle($roleNamesBegins);
        shuffle($roleNamesEndings);
        $name = sprintf('%s %s %s', $roleNamesBegins[0], $specialization->getTitle(), $roleNamesEndings[0]);
        $role->setName($name);
        $role->setSpecialization($specialization);
        $role->setDescription($this->faker->text(rand(2, 1000)));
        $role->setVacant(rand(0, 1));
        $role->setProject($project);
        $skillsCount = rand(0, 10);
        for ($i = 0; $i < $skillsCount; ++$i) {
            $skill = array_pop($skills);
            $projectOpenRoleSkills = new ProjectOpenRoleSkills();
            $projectOpenRoleSkills->setOpenRole($role);
            $projectOpenRoleSkills->setSkill($skill);
            $projectOpenRoleSkills->setPriority($i);
            $this->entityManager->persist($projectOpenRoleSkills);
        }
        $this->entityManager->persist($role);

        return $role;
    }

    /**
     * @param Project $project
     *
     * @return ProjectScreen
     */
    private function createScreen(Project $project)
    {
        $image = $this->faker->imageUrl();
        $screen = new ProjectScreen();
        $screen->setScreenshot($image);
        $screen->setProject($project);
        $this->entityManager->persist($screen);

        return $screen;
    }
}
