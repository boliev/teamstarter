<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="project_open_role_skills")
 */
class ProjectOpenRoleSkills
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ProjectOpenRole
     * @ORM\ManyToOne(targetEntity="ProjectOpenRole", inversedBy="skills")
     */
    private $openRole;

    /**
     * @var Skill
     * @ORM\ManyToOne(targetEntity="App\Entity\Skill", inversedBy="projectOpenRoleSkills")
     */
    private $skill;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $priority;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return ProjectOpenRole
     */
    public function getOpenRole(): ProjectOpenRole
    {
        return $this->openRole;
    }

    /**
     * @param ProjectOpenRole $openRole
     */
    public function setOpenRole(ProjectOpenRole $openRole)
    {
        $this->openRole = $openRole;
    }

    /**
     * @return Skill
     */
    public function getSkill(): Skill
    {
        return $this->skill;
    }

    /**
     * @param Skill $skill
     */
    public function setSkill(Skill $skill)
    {
        $this->skill = $skill;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority(int $priority)
    {
        $this->priority = $priority;
    }

    public function __toString()
    {
        return $this->getSkill()->getTitle();
    }
}
