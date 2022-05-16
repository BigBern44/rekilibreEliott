<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RegistrationRepository")
 */
class Registration
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthdate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $emailAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $zipCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $boardCandidate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $agreePhoto;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="registrations")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateValidate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="boolean")
     */
    private $subscriber;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Activity", inversedBy="registrations")
     */
    private $activities;

    /**
     * @ORM\Column(type="boolean")
     */
    private $membershipCheck;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activitiesSingleCheck;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activitiesMultiChecks;

    private $dateCloseSeason; 

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
        $this->activities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlreadyMember(): ?bool
    {
        return $this->alreadyMember;
    }

    public function setAlreadyMember(bool $alreadyMember): self
    {
        $this->alreadyMember = $alreadyMember;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): self
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function getPostAddress(): ?string
    {
        return $this->postAddress;
    }

    public function setPostAddress(string $postAddress): self
    {
        $this->postAddress = $postAddress;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getBoardCandidate(): ?bool
    {
        return $this->boardCandidate;
    }

    public function setBoardCandidate(bool $boardCandidate): self
    {
        $this->boardCandidate = $boardCandidate;

        return $this;
    }

    public function getAgreePhoto(): ?bool
    {
        return $this->agreePhoto;
    }

    public function setAgreePhoto(bool $agreePhoto): self
    {
        $this->agreePhoto = $agreePhoto;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDateCreate(): ?\DateTimeInterface
    {
        return $this->dateCreate;
    }

    public function setDateCreate(?\DateTimeInterface $dateCreate): self
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    public function getDateValidate(): ?\DateTimeInterface
    {
        return $this->dateValidate;
    }

    public function setDateValidate(\DateTimeInterface $dateValidate): self
    {
        $this->dateValidate = $dateValidate;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getSubscriber(): ?bool
    {
        return $this->subscriber;
    }

    public function setSubscriber(bool $subscriber): self
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
        }

        return $this;
    }

    public function getMembershipCheck(): ?bool
    {
        return $this->membershipCheck;
    }

    public function setMembershipCheck(bool $membershipCheck): self
    {
        $this->membershipCheck = $membershipCheck;

        return $this;
    }

    public function getActivitiesSingleCheck(): ?bool
    {
        return $this->activitiesSingleCheck;
    }

    public function setActivitiesSingleCheck(bool $activitiesSingleCheck): self
    {
        $this->activitiesSingleCheck = $activitiesSingleCheck;

        return $this;
    }

    public function getActivitiesMultiChecks(): ?bool
    {
        return $this->activitiesMultiChecks;
    }

    public function setActivitiesMultiChecks(bool $activitiesMultiChecks): self
    {
        $this->activitiesMultiChecks = $activitiesMultiChecks;

        return $this;
    }

    public function getDateCloseSeason(): ? \DateTimeInterface
    {
        return $this->dateCloseSeason;
    }

    public function setDateCloseSeason(\DateTimeInterface $dateCloseSeason): self
    {
        $this->dateCloseSeason = $dateCloseSeason;

        return $this;
    }


}
