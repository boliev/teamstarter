<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="project_screens")
 */
class ProjectScreen
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
     * @ORM\Column(name="screenshot", type="string", length=255, nullable=true)
     */
    private $screenshot;

    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="screens")
     */
    private $project;

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
     * @return string
     */
    public function getScreenshot(): string
    {
        return $this->screenshot;
    }

    /**
     * @param string $screenshot
     */
    public function setScreenshot(string $screenshot)
    {
        $this->screenshot = $screenshot;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @param Project $project
     */
    public function setProject(Project $project)
    {
        $this->project = $project;
    }

    public function __toString() {
        return $this->screenshot;
    }
}
