<?php

namespace App\Entity;

use App\Repository\OffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: OffreRepository::class)]
class Offre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @Groups({"offre"})
     */
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Type is required")]
    /**
     * @Groups({"offre"})
     */
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 12, minMessage: "La description de l'évènement doit comporter au moins {{ limit }} caractéres")]
    /**
     * @Groups({"offre"})
     */
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\GreaterThan('today')]
    /**
     * @Groups({"offre"})
     */
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Type is required")]
    /**
     * @Groups({"offre"})
     */
    private ?string $img = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: "Type is required")]
    /**
     * @Groups({"offre"})
     */
    private ?string $lieu = null;

    #[ORM\ManyToOne(inversedBy: 'offres')]
    /**
     * @Groups({"offre"})
     */
    private ?Coach $coach = null;

    #[ORM\Column]
    private ?float $pos1 = null;

    #[ORM\Column]
    private ?float $pos2 = null;

    #[ORM\OneToMany(mappedBy: 'offre', targetEntity: Participant::class, orphanRemoval: true)]
    private Collection $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getCoach(): ?Coach
    {
        return $this->coach;
    }

    public function setCoach(?Coach $coach): self
    {
        $this->coach = $coach;

        return $this;
    }


    public function __toString()
    {
        return $this->getTitre();
    }

    public function getPos1(): ?float
    {
        return $this->pos1;
    }

    public function setPos1(float $pos1): self
    {
        $this->pos1 = $pos1;

        return $this;
    }

    public function getPos2(): ?float
    {
        return $this->pos2;
    }

    public function setPos2(float $pos2): self
    {
        $this->pos2 = $pos2;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipants(Participant $participants): static
    {
        if (!$this->participants->contains($participants)) {
            $this->participants->add($participants);
            $participants->setOffre($this);
        }

        return $this;
    }

    public function removeParticipants(Participant $participants): static
    {
        if ($this->participants->removeElement($participants)) {
            // set the owning side to null (unless already changed)
            if ($participants->getOffre() === $this) {
                $participants->setOffre(null);
            }
        }

        return $this;
    }


}
