<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="promo_codes")
 */
class PromoCode
{
    /**
     * @ORM\Id
     *
     * @var string|null
     * @ORM\Column(type="string", nullable=false)
     */
    private $code;

    /**
     * @var string|null
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var Collection|null
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="promoCode")
     */
    private $users;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default": 0})
     */
    private $freeProMonths;

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
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
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Collection
     */
    public function getUsers(): ?Collection
    {
        return $this->users;
    }

    /**
     * @param Collection $users
     */
    public function setUsers(Collection $users): void
    {
        $this->users = $users;
    }

    /**
     * @return mixed
     */
    public function getFreeProMonths()
    {
        return $this->freeProMonths;
    }

    /**
     * @param mixed $freeProMonths
     */
    public function setFreeProMonths($freeProMonths): void
    {
        $this->freeProMonths = $freeProMonths;
    }
}
