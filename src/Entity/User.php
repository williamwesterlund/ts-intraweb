<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
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
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_teacher;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_admin;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Client", mappedBy="teacher")
     * @MaxDepth(1);
     */
    private $clients;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StudentReport", mappedBy="teacher", orphanRemoval=true)
     * @MaxDepth(1);
     */
    private $studentReports;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\NewsPost", mappedBy="author")
     */
    private $newsPosts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author", orphanRemoval=true)
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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

    public function getIsTeacher(): ?bool
    {
        return $this->is_teacher;
    }

    public function setIsTeacher(bool $is_teacher): self
    {
        $this->is_teacher = $is_teacher;

        return $this;
    }

    public function getIsAdmin(): ?bool
    {
        return $this->is_admin;
    }

    public function setIsAdmin(bool $is_admin): self
    {
        $this->is_admin = $is_admin;

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
}
