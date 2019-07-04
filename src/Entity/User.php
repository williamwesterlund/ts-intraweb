<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Client", mappedBy="teacher")
     * @MaxDepth(1)
     */
    private $clients;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StudentReport", mappedBy="teacher", orphanRemoval=true)
     * @MaxDepth(1)
     */
    private $studentReports;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\NewsPost", mappedBy="author")
     * @MaxDepth(1)
     */
    private $newsPosts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author", orphanRemoval=true)
     * @MaxDepth(1)
     */
    private $comments;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->studentReports = new ArrayCollection();
        $this->newsPosts = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

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
     * @return Collection|Client[]
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
            $client->setTeacher($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->contains($client)) {
            $this->clients->removeElement($client);
            // set the owning side to null (unless already changed)
            if ($client->getTeacher() === $this) {
                $client->setTeacher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|StudentReport[]
     */
    public function getStudentReports(): Collection
    {
        return $this->studentReports;
    }

    public function addStudentReport(StudentReport $studentReport): self
    {
        if (!$this->studentReports->contains($studentReport)) {
            $this->studentReports[] = $studentReport;
            $studentReport->setTeacher($this);
        }

        return $this;
    }

    public function removeStudentReport(StudentReport $studentReport): self
    {
        if ($this->studentReports->contains($studentReport)) {
            $this->studentReports->removeElement($studentReport);
            // set the owning side to null (unless already changed)
            if ($studentReport->getTeacher() === $this) {
                $studentReport->setTeacher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|NewsPost[]
     */
    public function getNewsPosts(): Collection
    {
        return $this->newsPosts;
    }

    public function addNewsPost(NewsPost $newsPost): self
    {
        if (!$this->newsPosts->contains($newsPost)) {
            $this->newsPosts[] = $newsPost;
            $newsPost->setAuthor($this);
        }

        return $this;
    }

    public function removeNewsPost(NewsPost $newsPost): self
    {
        if ($this->newsPosts->contains($newsPost)) {
            $this->newsPosts->removeElement($newsPost);
            // set the owning side to null (unless already changed)
            if ($newsPost->getAuthor() === $this) {
                $newsPost->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

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

}
