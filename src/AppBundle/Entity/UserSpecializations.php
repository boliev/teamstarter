<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_specializations")
 */
class UserSpecializations
{
    const MAX_FOR_USER = 3;
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="userSpecializations")
     */
    private $user;

    /**
     * @var Specialization
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Specialization", inversedBy="userSpecializations")
     */
    private $specialization;

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
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return Specialization
     */
    public function getSpecialization(): Specialization
    {
        return $this->specialization;
    }

    /**
     * @param Specialization $specialization
     */
    public function setSpecialization(Specialization $specialization)
    {
        $this->specialization = $specialization;
    }

    public function __toString()
    {
        return $this->getSpecialization()->getTitle();
    }
}
