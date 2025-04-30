<?php

namespace App\Entity;

<<<<<<< HEAD
use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Reservations;

#[ORM\Entity]
class Formation
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $start_date;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $end_date;

    public function getId()
=======
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

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 5, minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z\s]+$/',
        message: 'Le titre ne doit contenir que des lettres et des espaces.'
    )]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 10, minMessage: 'La description doit contenir au moins {{ limit }} caractères.')]
    #[Assert\Regex(
        pattern: '/^[^\d]*$/',
        message: 'La description ne doit pas contenir de chiffres.'
    )]
    private ?string $description ;

    #[ORM\Column(type: 'datetime', nullable: false)]

    #[Assert\GreaterThanOrEqual('today', message: 'La date de début doit être aujourd\'hui ou dans le futur.')]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: 'datetime', nullable: true)]

    #[Assert\Expression(
        "this.getEndDate() > this.getStartDate()",
        message: "La date de fin doit être postérieure à la date de début."
    )]
    private ?\DateTimeInterface $endDate = null;



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
>>>>>>> origin/gestions-formations
    {
        return $this->id;
    }

<<<<<<< HEAD
    public function setId($value)
    {
        $this->id = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function getDescription()
=======
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
>>>>>>> origin/gestions-formations
    {
        return $this->description;
    }

<<<<<<< HEAD
    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getStart_date()
    {
        return $this->start_date;
    }

    public function setStart_date($value)
    {
        $this->start_date = $value;
    }

    public function getEnd_date()
    {
        return $this->end_date;
    }

    public function setEnd_date($value)
    {
        $this->end_date = $value;
    }

    #[ORM\OneToMany(mappedBy: "formation_id", targetEntity: Reservations::class)]
    private Collection $reservationss;

        public function getReservationss(): Collection
        {
            return $this->reservationss;
        }
    
        public function addReservations(Reservations $reservations): self
        {
            if (!$this->reservationss->contains($reservations)) {
                $this->reservationss[] = $reservations;
                $reservations->setFormation_id($this);
            }
    
            return $this;
        }
    
        public function removeReservations(Reservations $reservations): self
        {
            if ($this->reservationss->removeElement($reservations)) {
                // set the owning side to null (unless already changed)
                if ($reservations->getFormation_id() === $this) {
                    $reservations->setFormation_id(null);
                }
            }
    
            return $this;
        }
}
=======
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }
    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }



    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }



    public function setEndDate(?\DateTimeInterface $endDate): self
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
>>>>>>> origin/gestions-formations
