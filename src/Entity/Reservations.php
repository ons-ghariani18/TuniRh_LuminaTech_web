<?php

namespace App\Entity;

<<<<<<< HEAD
use Doctrine\ORM\Mapping as ORM;

use App\Entity\Formation;

#[ORM\Entity]
class Reservations
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

        #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "reservationss")]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id_user', onDelete: 'CASCADE')]
    private Users $user_id;

        #[ORM\ManyToOne(targetEntity: Formation::class, inversedBy: "reservationss")]
    #[ORM\JoinColumn(name: 'formation_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Formation $formation_id;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_inscription;

    public function getId()
=======
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Repository\ReservationsRepository')]
class Reservations
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private Users $user;

    #[ORM\ManyToOne(targetEntity: Formation::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private Formation $formation;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $reservationDate;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status; // e.g., 'pending', 'confirmed', 'cancelled'

    public function __construct()
    {
        $this->reservationDate = new \DateTime();
        $this->status = 'pending';
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

    public function getUser_id()
    {
        return $this->user_id;
    }

    public function setUser_id($value)
    {
        $this->user_id = $value;
    }

    public function getFormation_id()
    {
        return $this->formation_id;
    }

    public function setFormation_id($value)
    {
        $this->formation_id = $value;
    }

    public function getDate_inscription()
    {
        return $this->date_inscription;
    }

    public function setDate_inscription($value)
    {
        $this->date_inscription = $value;
    }
}
=======
    public function getUser(): Users
    {
        return $this->user;
    }

    public function setUser(Users $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getFormation(): Formation
    {
        return $this->formation;
    }

    public function setFormation(Formation $formation): self
    {
        $this->formation = $formation;
        return $this;
    }

    public function getReservationDate(): \DateTimeInterface
    {
        return $this->reservationDate;
    }

    public function setReservationDate(\DateTimeInterface $reservationDate): self
    {
        $this->reservationDate = $reservationDate;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
}
>>>>>>> origin/gestions-formations
