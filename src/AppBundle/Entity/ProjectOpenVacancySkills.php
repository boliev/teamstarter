<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="project_open_vacancy_skills")
 */
class ProjectOpenVacancySkills
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ProjectOpenVacancy
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProjectOpenVacancy", inversedBy="skills")
     */
    private $vacancy;

    /**
     * @var Skill
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Skill", inversedBy="projectOpenVacancySkills")
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
     * @return ProjectOpenVacancy
     */
    public function getVacancy(): ProjectOpenVacancy
    {
        return $this->vacancy;
    }

    /**
     * @param ProjectOpenVacancy $vacancy
     */
    public function setVacancy(ProjectOpenVacancy $vacancy)
    {
        $this->vacancy = $vacancy;
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
