<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectOpenRoleRepository")
 * @ORM\Table(name="project_open_roles")
 */
class ProjectOpenRole
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="openRoles")
     */
    private $project;

    /**
     * @var Specialization
     * @ORM\ManyToOne(targetEntity="App\Entity\Specialization", inversedBy="projectOpenRoles")
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
     * @ORM\OneToMany(targetEntity="ProjectOpenRoleSkills", mappedBy="openRole")
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
     * @param Collection $skills
     */
    public function setSkills(Collection $skills)
    {
        $this->skills = $skills;
    }

    public function __toString() {
        return $this->specialization->getTitle();
    }
}
