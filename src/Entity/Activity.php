<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Service\Constants\DayString;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\User;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivityRepository")
 */
class Activity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="activities")
     * @ORM\OrderBy({"surname" = "ASC","firstname" = "ASC"})
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="activities")
     */
    private $location;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxRegistrations;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="intervenerActivities")
     */
    private $interveners;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $day;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fromDateTime;

    /**
     * @ORM\Column(type="datetime")
     */
    private $toDateTime;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Session", mappedBy="activity")
     */
    private $sessions;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $fromTime;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $toTime;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Picture")
     */
    private $picture;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Registration", mappedBy="activities")
     */
    private $registrations;

    /**
     * @ORM\Column(type="boolean")
     */
    private $reiki;

    /**
     * @ORM\Column(type="integer")
     */
    private $year_id;
    
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->interveners = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->registrations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getMaxRegistrations(): ?int
    {
        return $this->maxRegistrations;
    }

    public function setMaxRegistrations(int $maxRegistrations): self
    {
        $this->maxRegistrations = $maxRegistrations;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addActivity($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeActivity($this);
        }

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
            $intervener->addIntervenerActivity($this);
        }

        return $this;
    }

    public function removeIntervener(User $intervener): self
    {
        if ($this->interveners->contains($intervener)) {
            $this->interveners->removeElement($intervener);
            $intervener->removeIntervenerActivity($this);
        }

        return $this;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(?string $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getFromDateTime(): ?\DateTimeInterface
    {
        return $this->fromDateTime;
    }

    public function setFromDateTime(\DateTimeInterface $fromDateTime): self
    {
        $this->fromDateTime = $fromDateTime;

        return $this;
    }

    public function getToDateTime(): ?\DateTimeInterface
    {
        return $this->toDateTime;
    }

    public function setToDateTime(\DateTimeInterface $toDateTime): self
    {
        $this->toDateTime = $toDateTime;

        return $this;
    }

    public function displayName(): string
    {
        $display = "";

        if(count($this->users) >= $this->maxRegistrations){
            $display = '[COMPLET] ';
        }

        if($this->type == 'hebdo'){
            $display = $display . $this->name . ' le ' .DayString::DAYSTRING[$this->day] . ' de ' . $this->fromTime->format('H:i') . ' à ' . $this->toTime->format('H:i') . ' (' . $this->fromDateTime->format('Y') . '/' . $this->toDateTime->format('Y') . ')';
        }
        else{
            $display = $display . $this->name . ' le ' . $this->fromDateTime->format('d/m/Y') . ' de ' . $this->fromTime->format('H:i') . ' à ' . $this->toTime->format('H:i');
        }

        return $display;
    }

    /**
     * @return Collection|Session[]
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->setActivity($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            // set the owning side to null (unless already changed)
            if ($session->getActivity() === $this) {
                $session->setActivity(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFromTime(): ?\DateTimeInterface
    {
        return $this->fromTime;
    }

    public function setFromTime(?\DateTimeInterface $fromTime): self
    {
        $this->fromTime = $fromTime;

        return $this;
    }

    public function getToTime(): ?\DateTimeInterface
    {
        return $this->toTime;
    }

    public function setToTime(?\DateTimeInterface $toTime): self
    {
        $this->toTime = $toTime;

        return $this;
    }

    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    public function setPicture(?Picture $picture): self
    {
        $this->picture = $picture;

        return $this;
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
            $registration->addActivity($this);
        }

        return $this;
    }

    public function removeRegistration(Registration $registration): self
    {
        if ($this->registrations->contains($registration)) {
            $this->registrations->removeElement($registration);
            $registration->removeActivity($this);
        }

        return $this;
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

        $result = '';

        if(count($this->users) >= $this->maxRegistrations){
            $result = '[COMPLET] ';
        }

        $result = $result . $this->getName() . ' le ' . $jours[$this->day] . ' de ' . $this->fromTime->format('H:i') . ' à ' . $this->toTime->format('H:i');

        foreach($this->interveners as $key => $intervener){
            if($key == 0){
                $result = $result . ' ' . $intervener->displayName();
            }
            else{
                $result = $result . '/' . $intervener->displayName();
            }
        }

        $result = $result . '(' . $this->getPrice() . '€)';

        return $result;
    }

    public function getReiki(): ?bool
    {
        return $this->reiki;
    }

    public function setReiki(bool $reiki): self
    {
        $this->reiki = $reiki;

        return $this;
    }

    public function getYearId(): ?int
    {
        return $this->reiki;
    }

    public function setYearId(int $year_id): self
    {
        $this->year_id = $year_id;

        return $this;
    }


    public function exportToPDF()
    {
        $activity = $this->getName();
        if($this->getType() == "hebdo")
        {
            $date = $this->getDay();
            switch($date)
            {
                case 1:
                    $jour="lundi";
                    break;
                case 2:
                    $jour="mardi";
                    break;
                case 3:
                    $jour="mercredi";
                    break;
                case 4:
                    $jour="jeudi";
                    break;
                case 5:
                    $jour="vendredi";
                    break;
                case 6:
                    $jour="samedi";
                    break;
                case 7:
                    $jour="dimanche";
                    break;
            }
        }
        else
        {
            $jour = $this->getToDateTime()->format('Y-m-d');
        }
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $users = $this->getUsers();
        $infoF = "<h1><center> Liste des participants à l'activité : ".$activity." du ".$jour."</center></h1>\n";
        $retourLigne = "\n";
        $tableaux = "<table><tr><td><b>Nom</b></td><td><b>Prenom</b></td><td><b>Email</b></td><td><b>Téléphone</b></td><td><b>Adresse</b></td><</tr>";
        $tableaux = $infoF.$tableaux;
        foreach($users as $user){
            $tableaux = $tableaux."<tr>";
            $tableaux = $tableaux."<td><b>".$user->getSurname()."</b></td>";
            $tableaux = $tableaux."<td>".$user->getFirstname()."</td>";
            $tableaux = $tableaux."<td><b>".$user->getEmail()."</b></td>";
            $tableaux = $tableaux."<td>".$user->getPhone()."</td>";
            $tableaux = $tableaux."<td><b>".$user->getFullAddress()."</b></td>";
            $tableaux = $tableaux."</tr>";
        }
        $tableaux = $tableaux."</table>";
        $dompdf->loadHtml($tableaux);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Liste des intervenants de ".$activity.".pdf", ["Attachment" => true ]);
    }
    
}
