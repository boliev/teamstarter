<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OfferRepository")
 * @ORM\Table(name="offers")
 * @ORM\HasLifecycleCallbacks()
 */
class Offer
{
    const STATUS_NEW = 'New';

    const ACTIVE_STATUSES = [
        self::STATUS_NEW,
    ];

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="outgoingOffers")
     */
    private $from;

    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="offers")
     */
    private $project;

    /**
     * @var ProjectOpenRole|null
     * @ORM\ManyToOne(targetEntity="App\Entity\ProjectOpenRole", inversedBy="offers")
     * @ORM\JoinColumn(name="project_open_role_id", referencedColumnName="id", nullable=true)
     */
    private $role;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="incomingOffers")
     */
    private $to;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false, options={"default": "New"})
     */
    private $status;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="offer")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $messages;

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
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->status = 'New';
        $this->setCreatedAt(new \DateTime('now'));
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @throws \Exception
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
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getFrom(): User
    {
        return $this->from;
    }

    /**
     * @param User $from
     */
    public function setFrom(User $from): void
    {
        $this->from = $from;
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
    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    /**
     * @return ProjectOpenRole|null
     */
    public function getRole(): ?ProjectOpenRole
    {
        return $this->role;
    }

    /**
     * @param ProjectOpenRole|null $role
     */
    public function setRole(?ProjectOpenRole $role): void
    {
        $this->role = $role;
    }

    /**
     * @return User
     */
    public function getTo(): ?User
    {
        return $this->to;
    }

    /**
     * @param User $to
     */
    public function setTo(User $to): void
    {
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
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
    public function setCreatedAt(\DateTime $createdAt): void
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
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Collection
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function isUserInvolved(User $user)
    {
        /** @var Message $message */
        $message = $this->getMessages()[0];
        if (
            $message->getFrom()->getId() === $user->getId() ||
            $message->getTo()->getId() === $user->getId()
        ) {
            return true;
        }

        return false;
    }
}
