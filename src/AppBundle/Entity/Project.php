<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="projects")
 */
class Project
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="projects")
     */
    private $user;

    /**
     * @var ProjectProgress
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProjectProgress", inversedBy="projects")
     */
    private $progress;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mission;

    /**
     * @var string
     * @ORM\Column(name="country", type="string", length=3, nullable=true)
     */
    private $country;

    /**
     * @var string
     * @ORM\Column(name="city", type="string", length=100, nullable=true)
     */
    private $city;

    /**
     * @var ProjectStatus
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProjectStatus", inversedBy="projects")
     */
    private $status;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProjectDoc", mappedBy="project")
     */
    private $docs;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProjectOpenVacancy", mappedBy="project")
     */
    private $openVacancies;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProjectProgress", mappedBy="project")
     */
    private $demos;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProjectScreen", mappedBy="project")
     */
    private $screens;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $isDeleted = false;

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
     * @return string | null
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
     * @return ProjectProgress
     */
    public function getProgress(): ProjectProgress
    {
        return $this->progress;
    }

    /**
     * @param ProjectProgress $progress
     */
    public function setProgress(ProjectProgress $progress)
    {
        $this->progress = $progress;
    }

    /**
     * @return string|null
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
     * @return string | null
     */
    public function getMission(): ?string
    {
        return $this->mission;
    }

    /**
     * @param string $mission
     */
    public function setMission(string $mission)
    {
        $this->mission = $mission;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country)
    {
        $this->country = $country;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city)
    {
        $this->city = $city;
    }

    /**
     * @return ProjectStatus|null
     */
    public function getStatus(): ?ProjectStatus
    {
        return $this->status;
    }

    /**
     * @param ProjectStatus $status
     */
    public function setStatus(ProjectStatus $status)
    {
        $this->status = $status;
    }

    /**
     * @return Collection
     */
    public function getDocs(): Collection
    {
        return $this->docs;
    }

    /**
     * @param Collection $docs
     */
    public function setDocs(Collection $docs)
    {
        $this->docs = $docs;
    }

    /**
     * @return Collection
     */
    public function getDemos(): Collection
    {
        return $this->demos;
    }

    /**
     * @param Collection $demos
     */
    public function setDemos(Collection $demos)
    {
        $this->demos = $demos;
    }

    /**
     * @return Collection
     */
    public function getScreens(): Collection
    {
        return $this->screens;
    }

    /**
     * @param Collection $screens
     */
    public function setScreens(Collection $screens)
    {
        $this->screens = $screens;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     */
    public function setIsDeleted(bool $isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Collection
     */
    public function getOpenVacancies(): Collection
    {
        return $this->openVacancies;
    }

    /**
     * @param Collection $openVacancies
     */
    public function setOpenVacancies(Collection $openVacancies)
    {
        $this->openVacancies = $openVacancies;
    }
}
