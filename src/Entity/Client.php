<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 */
class Client
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
    private $parent_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $student_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $level;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subjects;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $study_plan;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $time;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="clients")
     */
    private $teacher;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StudentReport", mappedBy="client", orphanRemoval=true)
     * @MaxDepth(1)
     */
    private $studentReports;

    public function __construct()
    {
        $this->studentReports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParentName(): ?string
    {
        return $this->parent_name;
    }

    public function setParentName(string $parent_name): self
    {
        $this->parent_name = $parent_name;

        return $this;
    }

    public function getStudentName(): ?string
    {
        return $this->student_name;
    }

    public function setStudentName(string $student_name): self
    {
        $this->student_name = $student_name;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getSubjects(): ?string
    {
        return $this->subjects;
    }

    public function setSubjects(string $subjects): self
    {
        $this->subjects = $subjects;

        return $this;
    }

    public function getStudyPlan(): ?string
    {
        return $this->study_plan;
    }

    public function setStudyPlan(string $study_plan): self
    {
        $this->study_plan = $study_plan;

        return $this;
    }

    public function getTime(): ?string
    {
        return $this->time;
    }

    public function setTime(string $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher(?User $teacher): self
    {
        $this->teacher = $teacher;

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
            $studentReport->setClient($this);
        }

        return $this;
    }

    public function removeStudentReport(StudentReport $studentReport): self
    {
        if ($this->studentReports->contains($studentReport)) {
            $this->studentReports->removeElement($studentReport);
            // set the owning side to null (unless already changed)
            if ($studentReport->getClient() === $this) {
                $studentReport->setClient(null);
            }
        }

        return $this;
    }
}
