<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="payments")
 */
class Payment
{
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCEEDED = 'succeeded';

    const TYPE_YANDEX = 'yandex';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $extId;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="payments")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false, options={"default": "pending"})
     */
    private $status;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $paid;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $currency;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    private $metadata;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $confirmUrl;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
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
     * @return string
     */
    public function getExtId(): string
    {
        return $this->extId;
    }

    /**
     * @param string $extId
     */
    public function setExtId(string $extId): void
    {
        $this->extId = $extId;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
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
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->paid;
    }

    /**
     * @param bool $paid
     */
    public function setPaid(bool $paid): void
    {
        $this->paid = $paid;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    /**
     * @param \DateTime $created_at
     */
    public function setCreatedAt(\DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array $metadata
     */
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    /**
     * @return string
     */
    public function getConfirmUrl(): string
    {
        return $this->confirmUrl;
    }

    /**
     * @param string $confirmUrl
     */
    public function setConfirmUrl(string $confirmUrl): void
    {
        $this->confirmUrl = $confirmUrl;
    }
}
