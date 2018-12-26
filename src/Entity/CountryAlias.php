<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="countries_alias")
 */
class CountryAlias
{
    /**
     * @ORM\Id
     *
     * @var Country
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", inversedBy="aliases")
     * @ORM\JoinColumn(name="country", referencedColumnName="code")
     */
    private $country;

    /**
     * @ORM\Id
     *
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

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

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return Country
     */
    public function getCountry(): Country
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }
}
