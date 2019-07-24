<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudentReportRepository")
 */
class StudentReport
{
    use TimestampableEntity;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="studentReports")
     * @ORM\JoinColumn(nullable=false)
     * @MaxDepth(1)
     */
    private $teacher;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="studentReports")
     * @ORM\JoinColumn(nullable=false)
     * @MaxDepth(1)
     */
    private $client;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateUntil;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $q1_subjects;

    /**
     * @ORM\Column(type="integer")
     */
    private $q2_performance;

    /**
     * @ORM\Column(type="integer")
     */
    private $q3_motivation;

    /**
     * @ORM\Column(type="text")
     */
    private $q4_trajectory;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeacherId(): ?int
    {
        return $this->teacher_id;
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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDateUntil(): ?\DateTimeInterface
    {
        return $this->dateUntil;
    }

    public function setDateUntil(\DateTimeInterface $dateUntil): self
    {
        $this->dateUntil = $dateUntil;

        return $this;
    }

    public function getQ1Subjects(): ?string
    {
        return $this->q1_subjects;
    }

    public function setQ1Subjects(string $q1_subjects): self
    {
        $this->q1_subjects = $q1_subjects;

        return $this;
    }

    public function getQ2Performance(): ?int
    {
        return $this->q2_performance;
    }

    public function setQ2Performance(int $q2_performance): self
    {
        $this->q2_performance = $q2_performance;

        return $this;
    }

    public function getQ3Motivation(): ?int
    {
        return $this->q3_motivation;
    }

    public function setQ3Motivation(int $q3_motivation): self
    {
        $this->q3_motivation = $q3_motivation;

        return $this;
    }

    public function getQ4Trajectory(): ?string
    {
        return $this->q4_trajectory;
    }

    public function setQ4Trajectory(string $q4_trajectory): self
    {
        $this->q4_trajectory = $q4_trajectory;

        return $this;
    }
}
