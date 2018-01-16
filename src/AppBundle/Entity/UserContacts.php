<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_contacts")
 */
class UserContacts
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="userContacts")
     */
    private $user;

    /**
     * @var Contact
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contact", inversedBy="userContacts")
     */
    private $contact;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $number;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $additional;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $visible = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $prefered = false;

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
     * @return null|Contact
     */
    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    /**
     * @param Contact $contact
     */
    public function setContact(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return string
     */
    public function getNumber(): ?String
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber(String $number)
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getAdditional(): ?String
    {
        return $this->additional;
    }

    /**
     * @param string $additional
     */
    public function setAdditional(?String $additional)
    {
        $this->additional = $additional;
    }

    /**
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     */
    public function setVisible(bool $visible)
    {
        $this->visible = $visible;
    }

    /**
     * @return bool
     */
    public function isPrefered(): bool
    {
        return $this->prefered;
    }

    /**
     * @param bool $prefered
     */
    public function setPrefered(bool $prefered)
    {
        $this->prefered = $prefered;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (null === $this->contact) {
            return '';
        }

        return $this->contact->getName();
    }
}
