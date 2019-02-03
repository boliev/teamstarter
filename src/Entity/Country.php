<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository")
 * @ORM\Table(name="country")
 */
class Country
{
    /**
     * @ORM\Id
     *
     * @var string
     * @ORM\Column(type="string", name="code", length=3)
     */
    private $code;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $ru;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\CountryAlias", mappedBy="country")
     */
    private $aliases;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="country")
     */
    private $users;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="country")
     */
    private $projects;

    /**
     * @return Collection
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param Collection $code
     */
    public function setCode(Collection $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return Collection
     */
    public function getAliases(): Collection
    {
        return $this->aliases;
    }

    /**
     * @param Collection $aliases
     */
    public function setAliases(Collection $aliases): void
    {
        $this->aliases = $aliases;
    }

    /**
     * @return Collection
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @return string
     */
    public function getRu(): string
    {
        return $this->ru;
    }

    /**
     * @param string $ru
     */
    public function setRu(string $ru): void
    {
        $this->ru = $ru;
    }
}
