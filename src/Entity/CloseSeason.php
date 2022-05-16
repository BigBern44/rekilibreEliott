<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CloseSeasonRepository")
 */
class CloseSeason
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $closeDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCloseDate(): ?\DateTimeInterface
    {
        return $this->closeDate;
    }

    public function setCloseDate(\DateTimeInterface $closeDate): self
    {
        $this->closeDate = $closeDate;

        return $this;
    }
}
