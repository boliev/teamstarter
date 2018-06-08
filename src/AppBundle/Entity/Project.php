<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectRepository")
 * @ORM\Table(name="projects")
 * @ORM\HasLifecycleCallbacks()
 */
class Project
{
    const STATUS_UNFINISHED = 'Unfinished';
    const STATUS_INREVIW = 'InReview';
    const STATUS_DECLINED = 'Declined';
    const STATUS_PUBLISHED = 'Published';
    const STATUS_CLOSED = 'Closed';
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
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false, options={"default": "Unfinished"})
     */
    private $progressStatus;

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
     * @ORM\OneToMany(targetEntity="ProjectOpenRole", mappedBy="project")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $openRoles;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProjectDemo", mappedBy="project")
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
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * Project constructor.
     */
    public function __construct()
    {
        $this->progressStatus = 'Unfinished';
        $this->setCreatedAt(new \DateTime('now'));
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTime('now'));
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Collection
     */
    public function getOpenRoles(): ?Collection
    {
        return $this->openRoles;
    }

    /**
     * @param Collection $openRoles
     */
    public function setOpenRoles(Collection $openRoles)
    {
        $this->openRoles = $openRoles;
    }

    /**
     * @return string
     */
    public function getProgressStatus(): string
    {
        return $this->progressStatus;
    }

    /**
     * @param string $progressStatus
     */
    public function setProgressStatus(string $progressStatus)
    {
        $this->progressStatus = $progressStatus;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
