<?php

namespace App\Entity;

<<<<<<< HEAD
use Doctrine\ORM\Mapping as ORM;
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
use App\Entity\Sponsor;
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421

use App\Entity\Sponsor;
use Doctrine\Common\Collections\Collection;
use App\Entity\Participation;
=======
use App\Entity\Sponsor;
>>>>>>> origin/gestionOffre
<<<<<<< HEAD
=======
use App\Entity\Sponsor;
>>>>>>> origin/gestionUserReclamation
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421

#[ORM\Entity]
class Evenement
{
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_ev;

        #[ORM\ManyToOne(targetEntity: Sponsor::class, inversedBy: "evenements")]
    #[ORM\JoinColumn(name: 'id_sp', referencedColumnName: 'id_sp', onDelete: 'CASCADE')]
    private Sponsor $id_sp;
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Sponsor;

#[ORM\Entity]
class Evenement {
>>>>>>> origin/gestions-formations
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_ev", type: "integer")]
    private ?int $id_ev = null;
<<<<<<< HEAD

    #[ORM\ManyToOne(targetEntity: Sponsor::class, inversedBy: "evenements")]
    #[ORM\JoinColumn(name: "id_sp", referencedColumnName: "id_sp", onDelete: "CASCADE")]
    private ?Sponsor $sponsor = null;
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation

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

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function getId_ev()
    {
        return $this->id_ev;
    }

    public function setId_ev($value)
    {
        $this->id_ev = $value;
    }

    public function getId_sp()
    {
        return $this->id_sp;
    }

    public function setId_sp($value)
    {
        $this->id_sp = $value;
    }

    public function getNom()
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
    
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
    
>>>>>>> origin/gestions-formations
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function getIdEv(): ?int
    {
        return $this->id_ev;
    }
    
    public function setIdEv(int $idEv): self
    {
        $this->id_ev = $idEv;
        return $this;
    }
<<<<<<< HEAD

=======
    
>>>>>>> origin/gestions-formations
    public function getSponsor(): ?Sponsor
    {
        return $this->sponsor;
    }
<<<<<<< HEAD

=======
    
>>>>>>> origin/gestions-formations
    public function setSponsor(?Sponsor $sponsor): self
    {
        $this->sponsor = $sponsor;
        return $this;
    }
<<<<<<< HEAD

    public function getNom(): string
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
    {
        return $this->nom;
    }

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setNom($value)
    {
        $this->nom = $value;
    }

    public function getType()
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
    
    public function getNom(): string
    {
        return $this->nom;
    }
    
>>>>>>> origin/gestions-formations
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }
<<<<<<< HEAD

    public function getType(): string
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
    {
        return $this->type;
    }

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setType($value)
    {
        $this->type = $value;
    }

    public function getDate_debut()
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
    
    public function getType(): string
    {
        return $this->type;
    }
    
>>>>>>> origin/gestions-formations
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }
<<<<<<< HEAD

    public function getDateDebut(): \DateTimeInterface
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
    {
        return $this->date_debut;
    }

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setDate_debut($value)
    {
        $this->date_debut = $value;
    }

    public function getDate_fin()
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
    
    public function getDateDebut(): \DateTimeInterface
    {
        return $this->date_debut;
    }
    
>>>>>>> origin/gestions-formations
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->date_debut = $dateDebut;
        return $this;
    }
<<<<<<< HEAD

    public function getDateFin(): \DateTimeInterface
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
    {
        return $this->date_fin;
    }

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setDate_fin($value)
    {
        $this->date_fin = $value;
    }

    public function getLieu()
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
    
    public function getDateFin(): \DateTimeInterface
    {
        return $this->date_fin;
    }
    
>>>>>>> origin/gestions-formations
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->date_fin = $dateFin;
        return $this;
    }
<<<<<<< HEAD

    public function getLieu(): string
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
    {
        return $this->lieu;
    }

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setLieu($value)
    {
        $this->lieu = $value;
    }

    public function getBudget()
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
    
    public function getLieu(): string
    {
        return $this->lieu;
    }
    
>>>>>>> origin/gestions-formations
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;
        return $this;
    }
<<<<<<< HEAD

    public function getBudget(): float
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
    {
        return $this->budget;
    }

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setBudget($value)
    {
        $this->budget = $value;
    }

    public function getDescription()
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
    
    public function getBudget(): float
    {
        return $this->budget;
    }
    
>>>>>>> origin/gestions-formations
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setBudget(float $budget): self
    {
        $this->budget = $budget;
        return $this;
    }
<<<<<<< HEAD

    public function getDescription(): string
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
    {
        return $this->description;
    }

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getGoogle_calendar_event_id()
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
    
    public function getDescription(): string
    {
        return $this->description;
    }
    
>>>>>>> origin/gestions-formations
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }
<<<<<<< HEAD

    public function getGoogleCalendarEventId(): string
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
    {
        return $this->google_calendar_event_id;
    }

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setGoogle_calendar_event_id($value)
    {
        $this->google_calendar_event_id = $value;
    }

    #[ORM\OneToMany(mappedBy: "id_ev", targetEntity: Participation::class)]
    private Collection $participations;

        public function getParticipations(): Collection
        {
            return $this->participations;
        }
    
        public function addParticipation(Participation $participation): self
        {
            if (!$this->participations->contains($participation)) {
                $this->participations[] = $participation;
                $participation->setId_ev($this);
            }
    
            return $this;
        }
    
        public function removeParticipation(Participation $participation): self
        {
            if ($this->participations->removeElement($participation)) {
                // set the owning side to null (unless already changed)
                if ($participation->getId_ev() === $this) {
                    $participation->setId_ev(null);
                }
            }
    
            return $this;
        }
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
    
    public function getGoogleCalendarEventId(): string
    {
        return $this->google_calendar_event_id;
    }
    
>>>>>>> origin/gestions-formations
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setGoogleCalendarEventId(string $googleCalendarEventId): self
    {
        $this->google_calendar_event_id = $googleCalendarEventId;
        return $this;
    }
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
}
=======
    
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
>>>>>>> origin/gestions-formations
