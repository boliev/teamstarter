<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SpecializationRepository")
 * @ORM\Table(name="specialization")
 */
class Specialization
{
    const ID_OTHER = 1000;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\UserSpecializations", mappedBy="specialization")
     */
    private $userSpecializations;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectOpenRole", mappedBy="specialization")
     */
    private $projectOpenRoles;

    /**
     * @return int
     */
    public function getId()
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

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * @return Collection
     */
    public function getUserSpecializations(): Collection
    {
        return $this->userSpecializations;
    }

    /**
     * @param Collection $userSpecializations
     */
    public function setUserSpecializations(Collection $userSpecializations)
    {
        $this->userSpecializations = $userSpecializations;
    }

    /**
     * @return Collection
     */
    public function getProjectOpenRoles(): Collection
    {
        return $this->projectOpenRoles;
    }

    /**
     * @param Collection $projectOpenRoles
     */
    public function setProjectOpenRoles(Collection $projectOpenRoles): void
    {
        $this->projectOpenRoles = $projectOpenRoles;
    }
}
