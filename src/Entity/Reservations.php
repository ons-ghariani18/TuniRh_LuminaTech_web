<?php

namespace App\Entity;

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
    {
        return $this->id;
    }

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