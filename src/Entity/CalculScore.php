<?php

namespace App\Entity;

use App\Repository\CalculScoreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CalculScoreRepository::class)]
class CalculScore
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $score = null;

    #[ORM\OneToOne(inversedBy: 'note', cascade: ['persist', 'remove'])]
    private ?Participant $id_student = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getIdStudent(): ?Participant
    {
        return $this->id_student;
    }

    public function setIdStudent(?Participant $id_student): self
    {
        $this->id_student = $id_student;

        return $this;
    }
}
