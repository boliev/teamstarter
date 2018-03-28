<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="project_open_vacancies")
 */
class ProjectOpenVacancy
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project", inversedBy="openVacancies")
     */
    private $project;

    /**
     * @var Specialization
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Specialization", inversedBy="projects")
     */
    private $specialization;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default": true})
     */
    private $vacant = true;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProjectOpenVacancySkills", mappedBy="vacancy")
     */
    private $skills;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
    }

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
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @param Project $project
     */
    public function setProject(Project $project)
    {
        $this->project = $project;
    }

    /**
     * @return Specialization
     */
    public function getSpecialization(): ?Specialization
    {
        return $this->specialization;
    }

    /**
     * @param Specialization $specialization
     */
    public function setSpecialization(Specialization $specialization)
    {
        $this->specialization = $specialization;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isVacant(): ?bool
    {
        return $this->vacant;
    }

    /**
     * @param bool $vacant
     */
    public function setVacant(bool $vacant)
    {
        $this->vacant = $vacant;
    }

    /**
     * @return Collection
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    /**
     * @param Skill $skills
     */
    public function setSkills(Skill $skills)
    {
        $this->skills = $skills;
    }
}
