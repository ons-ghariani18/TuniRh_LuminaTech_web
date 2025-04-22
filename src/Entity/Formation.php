<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\Repository\FormationRepository')]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Length(min: 5, minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z\s]+$/',
        message: 'Le titre ne doit contenir que des lettres et des espaces.'
    )]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 20, minMessage: 'La description doit contenir au moins {{ limit }} caractères.')]
    #[Assert\Regex(
        pattern: '/^[^\d]*$/',
        message: 'La description ne doit pas contenir de chiffres.'
    )]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeInterface::class)]
    #[Assert\GreaterThanOrEqual('today', message: 'La date de début doit être aujourd\'hui ou dans le futur.')]
    private \DateTimeInterface $startDate;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeInterface::class)]
    #[Assert\Expression(
        "this.getEndDate() > this.getStartDate()",
        message: "La date de fin doit être postérieure à la date de début."
    )]
    private \DateTimeInterface $endDate;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull]
    #[Assert\GreaterThan(value: 0, message: 'Le nombre de participants doit être supérieur à 0.')]
    private int $maxParticipants;

    #[ORM\OneToMany(mappedBy: 'formation', targetEntity: Reservations::class)]
    private Collection $reservations;

    #[ORM\Column]
    private ?float $cost = null;

    #[ORM\OneToOne(mappedBy: 'formation', targetEntity: Quiz::class, cascade: ['persist', 'remove'])]
    private ?Quiz $quiz = null;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }
    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;
        return $this;
    }

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): \DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getMaxParticipants(): int
    {
        return $this->maxParticipants;
    }

    public function setMaxParticipants(int $maxParticipants): self
    {
        $this->maxParticipants = $maxParticipants;
        return $this;
    }

    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservations $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setFormation($this);
        }
        return $this;
    }

    public function removeReservation(Reservations $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getFormation() === $this) {
                $reservation->setFormation(null);
            }
        }
        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(float $cost): static
    {
        $this->cost = $cost;

        return $this;
    }
    public function __toString(): string
    {
        return $this->title; // or customize like "{$this->title} ({$this->startDate->format('Y-m-d')})"
    }

}