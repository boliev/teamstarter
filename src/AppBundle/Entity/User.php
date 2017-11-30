<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    public function setEmail($email)
    {
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
    }

    public function setEmailCanonical($emailCanonical){
        parent::setEmailCanonical($emailCanonical);
        $this->usernameCanonical = $emailCanonical;
    }
}