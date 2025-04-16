<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Sponsor;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
#[UniqueEntity(fields: ['nom'], message: 'Un événement avec ce nom existe déjà.')]
#[Assert\Callback('validateDates')]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_ev", type: "integer")]
    private ?int $id_ev = null;

    #[ORM\ManyToOne(targetEntity: Sponsor::class, inversedBy: "evenements")]
    #[ORM\JoinColumn(name: "id_sp", referencedColumnName: "id_sp", onDelete: "CASCADE")]
    #[Assert\NotNull(message: "Le sponsor est obligatoire.")]
    private ?Sponsor $sponsor = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private string $nom;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: "Le type est obligatoire.")]
    private string $type;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de début est obligatoire.")]
    private ?\DateTimeInterface $date_debut = null;
    
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de fin est obligatoire.")]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank(message: "Le lieu est obligatoire.")]
    #[Assert\Length(
        min: 3,
        minMessage: "Le lieu doit contenir au moins {{ limit }} caractères."
    )]
    private string $lieu;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\NotNull(message: "Le budget est obligatoire.")]
    #[Assert\PositiveOrZero(message: "Le budget ne peut pas être négatif.")]
    #[Assert\Type(
        type: "numeric",
        message: "Le budget doit être un nombre."
    )]
    private float $budget;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(
        min: 10,
        minMessage: "La description doit contenir au moins {{ limit }} caractères."
    )]
    private string $description;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $image = null;

    // Validation personnalisée des dates
    public function validateDates(ExecutionContextInterface $context): void
    {
        $now = new \DateTime('today');
    
        if ($this->date_debut === null || $this->date_fin === null) {
            return;
        }
    
        if ($this->date_debut < $now) {
            $context->buildViolation("La date de début ne peut pas être dans le passé.")
                ->atPath('date_debut')
                ->addViolation();
        }
    
        if ($this->date_debut > $this->date_fin) {
            $context->buildViolation("La date de fin doit être postérieure ou égale à la date de début.")
                ->atPath('date_fin')
                ->addViolation();
        }
    }

    // Getters et Setters
    public function getIdEv(): ?int
    {
        return $this->id_ev;
    }

    public function setIdEv(int $id_ev): self
    {
        $this->id_ev = $id_ev;
        return $this;
    }

    public function getSponsor(): ?Sponsor
    {
        return $this->sponsor;
    }

    public function setSponsor(?Sponsor $sponsor): self
    {
        $this->sponsor = $sponsor;
        return $this;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(?\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(?\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;
        return $this;
    }

    public function getLieu(): string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function getBudget(): float
    {
        return $this->budget;
    }

    public function setBudget(float $budget): self
    {
        $this->budget = $budget;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }
}