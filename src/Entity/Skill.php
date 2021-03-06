<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="skill")
 */
class Skill
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
    private $slug;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\UserSkills", mappedBy="skill")
     */
    private $userSkills;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return Collection
     */
    public function getUserSkills(): Collection
    {
        return $this->userSkills;
    }

    /**
     * @param Collection $userSkills
     */
    public function setUserSkills(Collection $userSkills)
    {
        $this->userSkills = $userSkills;
    }
}
