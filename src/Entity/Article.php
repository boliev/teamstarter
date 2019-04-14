<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @ORM\Table(name="articles")
 * @ORM\HasLifecycleCallbacks()
 */
class Article
{
    const STATUS_DRAFT = 'Draft';
    const STATUS_PUBLISHED = 'Published';
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="articles")
     */
    private $author;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\ArticleImage", mappedBy="article")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $images;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $removed;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $tempLink;

    /**
     * @var integer|null
     * @ORM\Column(type="integer", nullable=false, options={"default": 0})
     */
    private $commentsCount;

    /**
     * @var integer|null
     * @ORM\Column(type="integer", nullable=false, options={"default": 0})
     */
    private $viewsCount;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime")
     */
    private $publishedAt;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * Project constructor.
     */
    public function __construct()
    {
        $this->setCreatedAt(new DateTime('now'));
        $this->setRemoved(false);
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new DateTime('now'));
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
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
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
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return bool
     */
    public function isRemoved(): bool
    {
        return $this->removed;
    }

    /**
     * @param bool $removed
     */
    public function setRemoved(bool $removed): void
    {
        $this->removed = $removed;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function isDraft()
    {
        return self::STATUS_DRAFT === $this->getStatus();
    }

    public function isPublished()
    {
        return self::STATUS_PUBLISHED === $this->getStatus();
    }

    /**
     * @return Collection
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * @param Collection $images
     */
    public function setImages(Collection $images): void
    {
        $this->images = $images;
    }

    /**
     * @return int
     */
    public function getCommentsCount(): int
    {
        return $this->commentsCount;
    }

    /**
     * @param int $commentsCount
     */
    public function setCommentsCount(int $commentsCount): void
    {
        $this->commentsCount = $commentsCount;
    }

    public function getShort(): string
    {
        preg_match("/^(.*)<cut \/>/uis", $this->text, $matches);
        if(isset($matches[1])) {
            return $matches[1];
        }
        return $this->text;
    }

    /**
     * @return DateTime
     */
    public function getPublishedAt(): ?DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param DateTime $publishedAt
     */
    public function setPublishedAt(?DateTime $publishedAt=null): void
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * @return string
     */
    public function getTempLink(): ?string
    {
        return $this->tempLink;
    }

    /**
     * @param string $tempLink
     */
    public function setTempLink(?string $tempLink): void
    {
        $this->tempLink = $tempLink;
    }

    /**
     * @return int|null
     */
    public function getViewsCount(): ?int
    {
        return $this->viewsCount;
    }

    /**
     * @param int|null $viewsCount
     */
    public function setViewsCount(?int $viewsCount): void
    {
        $this->viewsCount = $viewsCount;
    }
}
