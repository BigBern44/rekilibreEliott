<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=255)
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
     * @ORM\Column(type="date")
     */
    private $birthdate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="boolean")
     */
    private $intervener;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $partner;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Activity", inversedBy="users")
     * @ORM\JoinTable(
     *  name="activity_user",
     *  joinColumns={
     *      @ORM\JoinColumn(name="activity_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *  })
     */
    private $activities;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payment", mappedBy="user")
     */
    private $payments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Activity", inversedBy="interveners")
     * @ORM\JoinTable(
     *  name="interveneractivity_intervener",
     *  joinColumns={
     *      @ORM\JoinColumn(name="interveneractivity_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="intervener_id", referencedColumnName="id")
     *  })
     */
    private $intervenerActivities;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Notification", mappedBy="users")
     */
    private $notifications;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Session", mappedBy="interveners")
     */
    private $sessions;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Picture")
     */
    private $picture;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Registration", mappedBy="user", cascade={"remove"})
     */
    private $registrations;

    /**
     * @ORM\Column(type="boolean")
     */
    private $anonymous;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tokenReset;

    /**
     * @ORM\OneToMany(targetEntity=Discussion::class, mappedBy="author_id", cascade={"persist"})
     */
    private $discussions;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="author")
     */
    private $posts;


    public function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->intervenerActivities = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->registrations = new ArrayCollection();
        $this->discussions = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function removeRoleMember(): self
    {
        if (($key = array_search('ROLE_ADHERENT', $this->roles)) !== false) {
            unset($this->roles[$key]);
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

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

    public function getEmailAddress(): ?string
    {
        return $this->email;
    }

    public function setEmailAddress(string $emailAddress): self
    {
        $this->email = $emailAddress;

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getIntervener(): ?bool
    {
        return $this->intervener;
    }

    public function setIntervener(bool $intervener): self
    {
        $this->intervener = $intervener;

        return $this;
    }

    public function getPartner(): ?string
    {
        return $this->partner;
    }

    public function setPartner(string $partner): self
    {
        $this->partner = $partner;

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
            $activity->addUser($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
            $activity->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setUser($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getUser() === $this) {
                $payment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getIntervenerActivities(): Collection
    {
        return $this->intervenerActivities;
    }

    public function addIntervenerActivity(Activity $intervenerActivity): self
    {
        if (!$this->intervenerActivities->contains($intervenerActivity)) {
            $this->intervenerActivities[] = $intervenerActivity;
            $intervenerActivity->addIntervener($this);
        }

        return $this;
    }

    public function removeIntervenerActivity(Activity $intervenerActivity): self
    {
        if ($this->intervenerActivities->contains($intervenerActivity)) {
            $this->intervenerActivities->removeElement($intervenerActivity);
            $intervenerActivity->removeIntervener($this);
        }

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->addUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            $notification->removeUser($this);
        }

        return $this;
    }

    public function getFullAddress(): string
    {
        return  $this->postAddress . ', ' . $this->zipCode . ' ' . $this->city;
    }

    public function displayName(): string
    {
        return $this->surname . ' ' . $this->firstname;
    }

    public function displayNameContact(): string
    {
        return $this->surname . ' ' . $this->firstname . ' / ' . $this->email . ' / ' . $this->phone . ' / ' . $this->postAddress . ', ' . $this->zipCode . ' ' . $this->city;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
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
            $session->addIntervener($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            $session->removeIntervener($this);
        }

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
            $registration->setUser($this);
        }

        return $this;
    }

    public function removeRegistration(Registration $registration): self
    {
        if ($this->registrations->contains($registration)) {
            $this->registrations->removeElement($registration);
            // set the owning side to null (unless already changed)
            if ($registration->getUser() === $this) {
                $registration->setUser(null);
            }
        }

        return $this;
    }

    public function fillWithRegistration(Registration $registration){
        $this->email = $registration->getEmailAddress();
        $this->roles = [
            '0' => 'ROLE_USER'
        ];
        $this->firstname = $registration->getFirstname();
        $this->surname = $registration->getLastname();
        $this->postAddress = $registration->getPostAddress();
        $this->zipCode = $registration->getZipCode();
        $this->city = $registration->getCity();
        $this->birthdate = $registration->getBirthdate();
        $this->status = false;
        $this->intervener = false;
        $this->gender = $registration->getGender();
        $this->phone = $registration->getPhone();
        $this->addRegistration($registration);
        $this->anonymous = true;
    }

    public function getAnonymous(): ?bool
    {
        return $this->anonymous;
    }

    public function setAnonymous(bool $anonymous): self
    {
        $this->anonymous = $anonymous;

        return $this;
    }

    public function getTokenReset(): ?string
    {
        return $this->tokenReset;
    }

    public function setTokenReset(?string $tokenReset): self
    {
        $this->tokenReset = $tokenReset;

        return $this;
    }
    public function getInfoUsers(): ?string
    {
        $lastname = $this->getSurname();
        $firstname = $this->getFirstname();
        $mail = $this->getEmail();
        $phone = $this->getPhone();
        $adress = $this->getFullAddress();
        $final = $lastname . '  ' . $firstname . ' / ' . $mail . ' / ' . $phone . ' / ' . $adress;
        return $final;


    }
    public function exportIntervenerToPDF($dump)
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($dump);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $Firstname = $this->getFirstname();
        $Lastname = $this->getSurname();
        $dompdf->stream($Firstname."_".$Lastname."_Liste_Des_Activitées.pdf", ["Attachment" => true ]);
    }

    /**
     * @return Collection|Discussion[]
     */
    public function getDiscussions(): Collection
    {
        return $this->discussions;
    }

    public function addDiscussion(Discussion $discussion): self
    {
        if (!$this->discussions->contains($discussion)) {
            $this->discussions[] = $discussion;
            $discussion->setAuthorId($this);
        }

        return $this;
    }

    public function removeDiscussion(Discussion $discussion): self
    {
        if ($this->discussions->removeElement($discussion)) {
            // set the owning side to null (unless already changed)
            if ($discussion->getAuthorId() === $this) {
                $discussion->setAuthorId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }

   

}
