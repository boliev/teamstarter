<?php

namespace App\Testing;

use App\Entity\Country;
use App\Entity\Project;
use App\Entity\ProjectOpenRole;
use App\Entity\ProjectOpenRoleSkills;
use App\Entity\ProjectStatus;
use App\Entity\Specialization;
use App\Entity\User;
use App\Repository\CountryRepository;
use App\Repository\SpecializationRepository;
use App\Service\SkillService;
use Doctrine\ORM\EntityManagerInterface;

class TestProjectCreator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CountryRepository
     */
    private $countryRepository;

    /**
     * @var SkillService
     */
    private $skillService;

    /**
     * @var SpecializationRepository
     */
    private $specializationRepository;

    /**
     * @param EntityManagerInterface   $entityManager
     * @param CountryRepository        $countryRepository
     * @param SpecializationRepository $specializationRepository
     * @param SkillService             $skillService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CountryRepository $countryRepository,
        SpecializationRepository $specializationRepository,
        SkillService $skillService
    ) {
        $this->entityManager = $entityManager;
        $this->countryRepository = $countryRepository;
        $this->skillService = $skillService;
        $this->specializationRepository = $specializationRepository;
    }

    /**
     * @param User  $user
     * @param array $projectData
     *
     * @return Project
     *
     * @throws \Exception
     */
    public function create(User $user, array $projectData)
    {
        $project = $this->createTestProject($user, $projectData);
        $this->addRoles($project, $projectData);

        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return $project;
    }

    /**
     * @param User  $user
     * @param array $projectData
     *
     * @return Project
     *
     * @throws \Exception
     */
    private function createTestProject(User $user, array $projectData): Project
    {
        if (!isset($projectData['status'])) {
            $projectData['status'] = 4;
        }

        /** @var ProjectStatus $projectStatus */
        $projectStatus = $this->entityManager->getRepository('App:ProjectStatus')->find($projectData['status']);
        /** @var Country $country */
        $country = $this->countryRepository->findOneBy(['code' => $projectData['country']]);
        $project = new Project();
        $project->setUser($user);
        $project->setName($projectData['name']);
        $project->setStatus($projectStatus);
        $project->setMission($projectData['mission']);
        $project->setDescription($projectData['description']);
        $project->setCountry($country);
        if (isset($projectData['city'])) {
            $project->setCity($projectData['city']);
        }
        $project->setProgressStatus(Project::STATUS_PUBLISHED);
        $this->entityManager->persist($project);

        return $project;
    }

    /**
     * @param Project $project
     * @param array   $projectData
     */
    private function addRoles(Project $project, array $projectData)
    {
        foreach ($projectData['roles'] as $roleData) {
            $specializationId = $roleData['specialization'] ?? 2;
            $specialization = $this->specializationRepository->find($specializationId);
            $skills = [];

            foreach ($roleData['skills'] as $skill) {
                $skills[] = $skillEntity = $this->skillService->getOrCreateSkill($skill);
            }

            /** @var Specialization $specialization */
            $role = new ProjectOpenRole();
            $role->setName($roleData['name']);
            $role->setSpecialization($specialization);
            $role->setDescription($roleData['description']);
            $role->setVacant(1);
            $role->setProject($project);
            $priority = 1;
            foreach ($skills as $skill) {
                $projectOpenRoleSkills = new ProjectOpenRoleSkills();
                $projectOpenRoleSkills->setOpenRole($role);
                $projectOpenRoleSkills->setSkill($skill);
                $projectOpenRoleSkills->setPriority($priority);
                $this->entityManager->persist($projectOpenRoleSkills);
                ++$priority;
            }
            $this->entityManager->persist($role);
        }
    }
}
