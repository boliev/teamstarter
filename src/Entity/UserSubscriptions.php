<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserSubscriptionsRepository")
 * @ORM\Table(name="user_subscriptions")
 */
class UserSubscriptions
{
    const EVENT_NEW_COMMENT_TO_POST_ADDED = 'new_comment_to_post_added';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userSpecializations")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $event;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $entityId;

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
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @param string $event
     */
    public function setEvent(string $event): void
    {
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function getEntityId(): string
    {
        return $this->entityId;
    }

    /**
     * @param string $entityId
     */
    public function setEntityId(string $entityId): void
    {
        $this->entityId = $entityId;
    }
}
