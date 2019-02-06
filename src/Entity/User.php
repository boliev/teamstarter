<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", name="first_name")
     */
    private $firstName;

    /**
     * @var string
     * @ORM\Column(type="string", name="last_name")
     */
    private $lastName;

    /**
     * @var string
     * @ORM\Column(name="facebook_id", type="string", nullable=true)
     */
    private $facebookId;

    /**
     * @var string
     * @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true)
     */
    protected $facebookAccessToken;

    /**
     * @var string
     * @ORM\Column(name="profile_picture", type="string", length=255, nullable=true)
     */
    protected $profilePicture;

    /**
     * @var string
     * @ORM\Column(name="google_access_token", type="string", length=255, nullable=true)
     */
    protected $googleAccessToken;

    /**
     * @var string
     * @ORM\Column(name="google_id", type="string", nullable=true)
     */
    private $googleId;

    /**
     * @var bool
     * @ORM\Column(name="password_auto_generated", type="boolean", options={"default": false})
     */
    private $passwordAutoGenerated = false;

    /**
     * @var string
     * @ORM\Column(name="github_access_token", type="string", length=255, nullable=true)
     */
    protected $githubAccessToken;

    /**
     * @var string
     * @ORM\Column(name="github_id", type="string", nullable=true)
     */
    private $githubId;

    /**
     * @var Country|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", inversedBy="users")
     * @ORM\JoinColumn(name="country", referencedColumnName="code")
     */
    private $country;

    /**
     * @var string
     * @ORM\Column(name="city", type="string", length=100, nullable=true)
     */
    private $city;

    /**
     * @var string
     * @ORM\Column(name="like_to_do", type="text", nullable=true)
     */
    private $likeToDo;

    /**
     * @var string
     * @ORM\Column(name="expectation", type="text", nullable=true)
     */
    private $expectation;

    /**
     * @var string
     * @ORM\Column(name="experience", type="text", nullable=true)
     */
    private $experience;

    /**
     * @var string
     * @ORM\Column(name="about", type="text", nullable=true)
     */
    private $about;

    /**
     * @var \DateTime
     * @ORM\Column(name="about_form_skipped", type="datetime", nullable=true)
     */
    private $aboutFormSkipped;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\UserSkills", orphanRemoval=true, mappedBy="user")
     */
    private $userSkills;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\UserSpecializations", orphanRemoval=true, mappedBy="user")
     * @ORM\OrderBy({"specialization" = "ASC"})
     */
    private $userSpecializations;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Project", orphanRemoval=true, mappedBy="user")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $projects;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $proUntil;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $search;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $isFake = false;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setCreatedAt(new \DateTime('now'));
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTime('now'));
    }

    /**
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    /**
     * @param string $facebookId
     */
    public function setFacebookId(string $facebookId)
    {
        $this->facebookId = $facebookId;
    }

    /**
     * @return string
     */
    public function getFacebookAccessToken(): ?string
    {
        return $this->facebookAccessToken;
    }

    /**
     * @param string $facebookAccessToken
     */
    public function setFacebookAccessToken(string $facebookAccessToken)
    {
        $this->facebookAccessToken = $facebookAccessToken;
    }

    /**
     * @return string
     */
    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    /**
     * @param string $profilePicture
     */
    public function setProfilePicture(string $profilePicture)
    {
        $this->profilePicture = $profilePicture;
    }

    /**
     * @return string
     */
    public function getGoogleAccessToken(): ?string
    {
        return $this->googleAccessToken;
    }

    /**
     * @param string $googleAccessToken
     */
    public function setGoogleAccessToken(string $googleAccessToken)
    {
        $this->googleAccessToken = $googleAccessToken;
    }

    /**
     * @return string
     */
    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    /**
     * @param string $googleId
     */
    public function setGoogleId(string $googleId)
    {
        $this->googleId = $googleId;
    }

    /**
     * @return bool
     */
    public function getPasswordAutoGenerated(): bool
    {
        return $this->passwordAutoGenerated;
    }

    /**
     * @param bool $passwordAutoGenerated
     */
    public function setPasswordAutoGenerated(bool $passwordAutoGenerated)
    {
        $this->passwordAutoGenerated = $passwordAutoGenerated;
    }

    /**
     * @return string
     */
    public function getGithubAccessToken(): ?string
    {
        return $this->githubAccessToken;
    }

    /**
     * @param string $githubAccessToken
     */
    public function setGithubAccessToken(string $githubAccessToken)
    {
        $this->githubAccessToken = $githubAccessToken;
    }

    /**
     * @return string
     */
    public function getGithubId(): ?string
    {
        return $this->githubId;
    }

    /**
     * @param string $githubId
     */
    public function setGithubId(string $githubId)
    {
        $this->githubId = $githubId;
    }

    /**
     * @return null|Country
     */
    public function getCountry(): ?Country
    {
        return $this->country;
    }

    /**
     * @param null|string $country
     */
    public function setCountry(?Country $country): void
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getAbout(): ?string
    {
        return $this->about;
    }

    /**
     * @param string $about
     */
    public function setAbout(string $about)
    {
        $this->about = $about;
    }

    /**
     * @return string
     */
    public function getLikeToDo(): ?string
    {
        return $this->likeToDo;
    }

    /**
     * @param string $likeToDo
     */
    public function setLikeToDo(string $likeToDo)
    {
        $this->likeToDo = $likeToDo;
    }

    /**
     * @return string
     */
    public function getExpectation(): ?string
    {
        return $this->expectation;
    }

    /**
     * @param string $expectation
     */
    public function setExpectation(string $expectation)
    {
        $this->expectation = $expectation;
    }

    /**
     * @return string
     */
    public function getExperience(): ?string
    {
        return $this->experience;
    }

    /**
     * @param string $experience
     */
    public function setExperience(string $experience)
    {
        $this->experience = $experience;
    }

    /**
     * @return \DateTime
     */
    public function getAboutFormSkipped(): \DateTime
    {
        if (null === $this->aboutFormSkipped) {
            return new \DateTime('1970-01-01 01:01:01');
        }

        return $this->aboutFormSkipped;
    }

    /**
     * @param \DateTime $aboutFormSkipped
     */
    public function setAboutFormSkipped(\DateTime $aboutFormSkipped)
    {
        $this->aboutFormSkipped = $aboutFormSkipped;
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

    public function getFullName()
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    /**
     * @return Collection
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    /**
     * @param Collection $projects
     */
    public function setProjects(Collection $projects)
    {
        $this->projects = $projects;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getSearch(): string
    {
        return $this->search;
    }

    /**
     * @param string $search
     */
    public function setSearch(string $search): void
    {
        $this->search = $search;
    }

    /**
     * @return bool
     */
    public function isFake(): bool
    {
        return $this->isFake;
    }

    /**
     * @param bool $isFake
     */
    public function setIsFake(bool $isFake): void
    {
        $this->isFake = $isFake;
    }

    public function isSpecialist()
    {
        if (true === $this->enabled and count($this->getUserSpecializations())) {
            return true;
        }

        return false;
    }

    /**
     * @return \DateTime
     */
    public function getProUntil(): ?\DateTime
    {
        return $this->proUntil;
    }

    /**
     * @param \DateTime $proUntil
     */
    public function setProUntil(\DateTime $proUntil): void
    {
        $this->proUntil = $proUntil;
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    public function isPro(): bool
    {
        if (null === $this->getProUntil()) {
            return false;
        }
        $nowDate = new \DateTime();

        return $this->getProUntil() >= $nowDate;
    }
}
