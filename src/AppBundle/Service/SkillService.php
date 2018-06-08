<?php

namespace AppBundle\Service;

use AppBundle\Entity\ProjectOpenRole;
use AppBundle\Entity\ProjectOpenRoleSkills;
use AppBundle\Entity\Skill;
use AppBundle\Entity\User;
use AppBundle\Entity\UserSkills;
use Doctrine\ORM\EntityManagerInterface;

class SkillService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SkillService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $slug
     *
     * @return string
     */
    public function generateSlug(string $slug): string
    {
        $slug = strtolower(trim($slug));
        $slug = preg_replace('/[^a-z0-9_]/ui', '_', $slug);

        return $slug;
    }

    /**
     * @param string $name
     *
     * @return Skill
     */
    public function getOrCreateSkill(string $name): Skill
    {
        $slug = $this->generateSlug($name);
        $skillEntity = $this->entityManager->getRepository(Skill::class)->findOneBy(['slug' => $slug]);
        if (!$skillEntity) {
            $skillEntity = new Skill();
            $skillEntity->setSlug($slug);
            $skillEntity->setTitle($name);
            $this->entityManager->persist($skillEntity);
            $this->entityManager->flush();
        }

        return $skillEntity;
    }

    /**
     * @param User $user
     */
    public function cleanSkillsForUser(User $user)
    {
        $userSkills = $this->entityManager->getRepository(UserSkills::class)->findBy(['user' => $user]);
        foreach ($userSkills as $userSkill) {
            $this->entityManager->remove($userSkill);
        }
        $this->entityManager->flush();
    }

    /**
     * @param ProjectOpenRole $role
     */
    public function cleanSkillsForProjectOpenRole(ProjectOpenRole $role)
    {
        $roleSkills = $this->entityManager->getRepository(ProjectOpenRoleSkills::class)
            ->findBy(['openRole' => $role]);
        foreach ($roleSkills as $roleSkill) {
            $this->entityManager->remove($roleSkill);
        }
        $this->entityManager->flush();
    }
}
