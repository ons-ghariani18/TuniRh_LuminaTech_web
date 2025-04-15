<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Sponsor;

#[ORM\Entity]
class Evenement {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_ev", type: "integer")]
    private ?int $id_ev = null;
    
    #[ORM\ManyToOne(targetEntity: Sponsor::class, inversedBy: "evenements")]
    #[ORM\JoinColumn(name: "id_sp", referencedColumnName: "id_sp", onDelete: "CASCADE")]
    private ?Sponsor $sponsor = null;
    
    #[ORM\Column(type: "string", length: 255)]
    private string $nom;
    
    #[ORM\Column(type: "string", length: 100)]
    private string $type;
    
    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_debut;
    
    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_fin;
    
    #[ORM\Column(type: "string", length: 255)]
    private string $lieu;
    
    #[ORM\Column(type: "float")]
    private float $budget;
    
    #[ORM\Column(type: "text")]
    private string $description;
    
    #[ORM\Column(type: "string", length: 255)]
    private string $google_calendar_event_id;
    
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $image = null;
    
    public function getIdEv(): ?int
    {
        return $this->id_ev;
    }
    
    public function setIdEv(int $idEv): self
    {
        $this->id_ev = $idEv;
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
    
    public function getDateDebut(): \DateTimeInterface
    {
        return $this->date_debut;
    }
    
    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->date_debut = $dateDebut;
        return $this;
    }
    
    public function getDateFin(): \DateTimeInterface
    {
        return $this->date_fin;
    }
    
    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->date_fin = $dateFin;
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
    
    public function getGoogleCalendarEventId(): string
    {
        return $this->google_calendar_event_id;
    }
    
    public function setGoogleCalendarEventId(string $googleCalendarEventId): self
    {
        $this->google_calendar_event_id = $googleCalendarEventId;
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