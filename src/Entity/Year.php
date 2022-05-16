<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\YearRepository")
 */
class Year
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=9, unique=true)
     */
    public $year;

    /**
     * @ORM\Column(type="date", nullable=true)
     */	
    private $firstPayment;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
	private $secondPayment;
	
    /**
     * @ORM\Column(type="date", nullable=true)
     */
	private $thirdPayment;
	
    /**
     * @ORM\Column(type="boolean")
     */
    private $is_current;

	public function getId(): ?int {
		return $this->id;
	}

	public function setId($id) {
        $this->id = $id;
        return $this;
	}

	public function getYear() {
		return $this->year;
	}

	public function setYear($year) {
        $this->year = $year;
        return $this;
	}

	public function getFirstDate() {
		return $this->firstPayment;
	}

	public function setFirstDate($firstPayment) {
        $this->firstPayment = $firstPayment;
        return $this;
	}

	public function getSecondDate() {
		return $this->secondPayment;
	}

	public function setSecondDate($secondPayment) {
        $this->secondPayment = $secondPayment;
        return $this;
	}

	public function getThirdDate() {
		return $this->thirdPayment;
	}

	public function setThirdDate($thirdPayment) {
        $this->thirdPayment = $thirdPayment;
        return $this;
	}

	public function getIsCurrent() {
		return $this->$isCurrent;
	}

	public function setIsCurrent($isCurrent) {
		$this->isCurrent = $isCurrent;
    }

    public function displayName(): string
    {
        return $this->year;
    }

    public function getCurrentYear()
    {
        $year = $this->createQueryBuilder('year.id')
                ->Where('year.is_current = 1');
        return $year;
    }
    
}


  

    