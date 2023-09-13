<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateParticipation = null;

    #[ORM\Column(length: 255)]
    private ?string $RBac = null;

    #[ORM\Column(length: 255)]
    private ?string $R1 = null;

    #[ORM\Column(length: 255)]
    private ?string $R2 = null;

    #[ORM\Column(length: 255)]
    private ?string $R3 = null;

    #[ORM\Column(length: 255)]
    private ?string $R4 = null;

    #[ORM\Column(length: 255)]
    private ?string $RL1 = null;

    #[ORM\Column(length: 255)]
    private ?string $RL2 = null;

    #[ORM\Column(length: 255)]
    private ?string $RL3 = null;

    #[ORM\Column(length: 255)]
    private ?string $niveauF = null;

    #[ORM\Column(length: 255)]
    private ?string $niveauA = null;

    #[ORM\Column(length: 255)]
    private ?string $nomp = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\OneToOne(mappedBy: 'id_student', cascade: ['persist', 'remove'])]
    private ?CalculScore $note = null;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Offre $offre = null;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDateParticipation(): ?\DateTimeInterface
    {
        return $this->dateParticipation;
    }

    public function setDateParticipation(\DateTimeInterface $dateParticipation): self
    {
        $this->dateParticipation = $dateParticipation;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $users): self
    {
        $this->user = $users;

        return $this;
    }

    public function getOffre(): ?Offre
    {
        return $this->offre;
    }

    public function setOffre(?Offre $offres): self
    {
        $this->offre = $offres;

        return $this;
    }

    public function getRBac(): ?string
    {
        return $this->RBac;
    }

    public function setRBac(string $RBac): self
    {
        $this->RBac = $RBac;

        return $this;
    }

    public function getR1(): ?string
    {
        return $this->R1;
    }

    public function setR1(string $R1): self
    {
        $this->R1 = $R1;

        return $this;
    }

    public function getR2(): ?string
    {
        return $this->R2;
    }

    public function setR2(string $R2): self
    {
        $this->R2 = $R2;

        return $this;
    }

    public function getR3(): ?string
    {
        return $this->R3;
    }

    public function setR3(string $R3): self
    {
        $this->R3 = $R3;

        return $this;
    }

    public function getR4(): ?string
    {
        return $this->R4;
    }

    public function setR4(string $R4): self
    {
        $this->R4 = $R4;

        return $this;
    }

    public function getRL1(): ?string
    {
        return $this->RL1;
    }

    public function setRL1(string $RL1): self
    {
        $this->RL1 = $RL1;

        return $this;
    }

    public function getRL2(): ?string
    {
        return $this->RL2;
    }

    public function setRL2(string $RL2): self
    {
        $this->RL2 = $RL2;

        return $this;
    }

    public function getRL3(): ?string
    {
        return $this->RL3;
    }

    public function setRL3(string $RL3): self
    {
        $this->RL3 = $RL3;

        return $this;
    }

    public function getNiveauF(): ?string
    {
        return $this->niveauF;
    }

    public function setNiveauF(string $niveauF): self
    {
        $this->niveauF = $niveauF;

        return $this;
    }

    public function getNiveauA(): ?string
    {
        return $this->niveauA;
    }

    public function setNiveauA(string $niveauA): self
    {
        $this->niveauA = $niveauA;

        return $this;
    }

    public function getNomp(): ?string
    {
        return $this->nomp;
    }

    public function setNomp(string $nomp): self
    {
        $this->nomp = $nomp;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

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

    public function getNote(): ?CalculScore
    {
        return $this->note;
    }

    public function setNote(?CalculScore $note): self
    {
        // unset the owning side of the relation if necessary
        if ($note === null && $this->note !== null) {
            $this->note->setIdStudent(null);
        }

        // set the owning side of the relation if necessary
        if ($note !== null && $note->getIdStudent() !== $this) {
            $note->setIdStudent($this);
        }

        $this->note = $note;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->getNom(),
            'offre' => $this->offre->getTitre(),
            'score' => $this->note->getScore(),
            'dateParticipation' => $this->dateParticipation?->format('Y-m-d'),
        ];
    }
}
