<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SessionRepository")
 */
class Session
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="time")
     */
    private $fromTime;

    /**
     * @ORM\Column(type="time")
     */
    private $toTime;

    /**
     * @ORM\Column(type="integer")
     */
    private $day;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="sessions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="sessions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $location;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="sessions")
     */
    private $interveners;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Registration", mappedBy="sessions")
     */
    private $registrations;

    public function __construct()
    {
        $this->interveners = new ArrayCollection();
        $this->registrations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromTime(): ?\DateTimeInterface
    {
        return $this->fromTime;
    }

    public function setFromTime(\DateTimeInterface $fromTime): self
    {
        $this->fromTime = $fromTime;

        return $this;
    }

    public function getToTime(): ?\DateTimeInterface
    {
        return $this->toTime;
    }

    public function setToTime(\DateTimeInterface $toTime): self
    {
        $this->toTime = $toTime;

        return $this;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getInterveners(): Collection
    {
        return $this->interveners;
    }

    public function addIntervener(User $intervener): self
    {
        if (!$this->interveners->contains($intervener)) {
            $this->interveners[] = $intervener;
        }

        return $this;
    }

    public function removeIntervener(User $intervener): self
    {
        if ($this->interveners->contains($intervener)) {
            $this->interveners->removeElement($intervener);
        }

        return $this;
    }

    public function displayName(){
        $jours = [
            '1' => 'lundi',
            '2' => 'mardi',
            '3' => 'mercredi',
            '4' => 'jeudi',
            '5' => 'vendredi',
            '6' => 'samedi',
            '7' => 'dimanche',
        ];

        return $this->activity->getName() . ' le ' . $jours[$this->day] . ' de ' . $this->fromTime->format('H:i') . ' à ' . $this->toTime->format('H:i');
    }

    public function displayRegistrationName(){
        $jours = [
            '1' => 'lundi',
            '2' => 'mardi',
            '3' => 'mercredi',
            '4' => 'jeudi',
            '5' => 'vendredi',
            '6' => 'samedi',
            '7' => 'dimanche',
        ];

        return $this->activity->getName() . ' le ' . $jours[$this->day] . ' de ' . $this->fromTime->format('H:i') . ' à ' . $this->toTime->format('H:i') . '(' . $this->activity->getPrice() . '€)';
    }

    /**
     * @return Collection|Registration[]
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function addRegistration(Registration $registration): self
    {
        if (!$this->registrations->contains($registration)) {
            $this->registrations[] = $registration;
            $registration->addSession($this);
        }

        return $this;
    }

    public function removeRegistration(Registration $registration): self
    {
        if ($this->registrations->contains($registration)) {
            $this->registrations->removeElement($registration);
            $registration->removeSession($this);
        }

        return $this;
    }

    public function getPrice(){
        return $this->activity->getPrice();
    }
}
