<?php

namespace App\Entity;

use App\Repository\DiscussionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=DiscussionRepository::class)
 */
class Discussion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="discussions")
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="Discussion",  cascade={"remove"})
     */
    private $posts;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToMany(targetEntity=CategorieDiscussion::class, inversedBy="discussions")
     */
    private $CategorieDiscussion;

    private $categorieAdmise;

    private $roleRequis;

    /**
     * @return mixed
     */
    public function getRoleRequis()
    {
        return $this->roleRequis;
    }

    /**
     * @param mixed $roleRequis
     */
    public function setRoleRequis($roleRequis): void
    {
        $this->roleRequis = $roleRequis;
    }



    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->CategorieDiscussion = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthorId(): ?User
    {
        return $this->author;
    }

    public function setAuthorId(?User $author): self
    {
        $this->author = $author;

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
            $post->setDiscussion($this);
        }

        return $this;
    }

    public function getCountPosts(): ?int
    {

        return count(  $this->getPosts() , COUNT_RECURSIVE );
    }


    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getDiscussion() === $this) {
                $post->setDiscussion(null);
            }
        }

        return $this;
    }

    public function getCreatedAt():?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|CategorieDiscussion[]
     */
    public function getCategorieDiscussion(): Collection
    {
        return $this->CategorieDiscussion;
    }

    public function addCategorieDiscussion(CategorieDiscussion $categorieDiscussion): self
    {
        if (!$this->CategorieDiscussion->contains($categorieDiscussion)) {
            $this->CategorieDiscussion[] = $categorieDiscussion;
        }

        return $this;
    }

    public function removeCategorieDiscussion(CategorieDiscussion $categorieDiscussion): self
    {
        $this->CategorieDiscussion->removeElement($categorieDiscussion);

        return $this;
    }
    public function getCategorieAdmise(): array
    {
        return $this->categorieAdmise;
    }

    public function setCategorieAdmise(array $categorieAdmise): self
    {
        $this->categorieAdmise = $categorieAdmise;

        return $this;
    }




}
