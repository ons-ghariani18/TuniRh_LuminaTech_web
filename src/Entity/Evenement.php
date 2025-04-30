<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
<<<<<<< HEAD
use App\Entity\Sponsor;
=======
<<<<<<< HEAD

use App\Entity\Sponsor;
use Doctrine\Common\Collections\Collection;
use App\Entity\Participation;
=======
use App\Entity\Sponsor;
>>>>>>> origin/gestionOffre
>>>>>>> origin/gestionOffre

#[ORM\Entity]
class Evenement
{
<<<<<<< HEAD
=======
<<<<<<< HEAD

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_ev;

        #[ORM\ManyToOne(targetEntity: Sponsor::class, inversedBy: "evenements")]
    #[ORM\JoinColumn(name: 'id_sp', referencedColumnName: 'id_sp', onDelete: 'CASCADE')]
    private Sponsor $id_sp;
=======
>>>>>>> origin/gestionOffre
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_ev", type: "integer")]
    private ?int $id_ev = null;

    #[ORM\ManyToOne(targetEntity: Sponsor::class, inversedBy: "evenements")]
    #[ORM\JoinColumn(name: "id_sp", referencedColumnName: "id_sp", onDelete: "CASCADE")]
    private ?Sponsor $sponsor = null;
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> origin/gestionOffre

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
=======
<<<<<<< HEAD
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
>>>>>>> origin/gestionOffre
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
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> origin/gestionOffre
    {
        return $this->nom;
    }

<<<<<<< HEAD
=======
<<<<<<< HEAD
    public function setNom($value)
    {
        $this->nom = $value;
    }

    public function getType()
=======
>>>>>>> origin/gestionOffre
    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getType(): string
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> origin/gestionOffre
    {
        return $this->type;
    }

<<<<<<< HEAD
=======
<<<<<<< HEAD
    public function setType($value)
    {
        $this->type = $value;
    }

    public function getDate_debut()
=======
>>>>>>> origin/gestionOffre
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getDateDebut(): \DateTimeInterface
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> origin/gestionOffre
    {
        return $this->date_debut;
    }

<<<<<<< HEAD
=======
<<<<<<< HEAD
    public function setDate_debut($value)
    {
        $this->date_debut = $value;
    }

    public function getDate_fin()
=======
>>>>>>> origin/gestionOffre
    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->date_debut = $dateDebut;
        return $this;
    }

    public function getDateFin(): \DateTimeInterface
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> origin/gestionOffre
    {
        return $this->date_fin;
    }

<<<<<<< HEAD
=======
<<<<<<< HEAD
    public function setDate_fin($value)
    {
        $this->date_fin = $value;
    }

    public function getLieu()
=======
>>>>>>> origin/gestionOffre
    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->date_fin = $dateFin;
        return $this;
    }

    public function getLieu(): string
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> origin/gestionOffre
    {
        return $this->lieu;
    }

<<<<<<< HEAD
=======
<<<<<<< HEAD
    public function setLieu($value)
    {
        $this->lieu = $value;
    }

    public function getBudget()
=======
>>>>>>> origin/gestionOffre
    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function getBudget(): float
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> origin/gestionOffre
    {
        return $this->budget;
    }

<<<<<<< HEAD
=======
<<<<<<< HEAD
    public function setBudget($value)
    {
        $this->budget = $value;
    }

    public function getDescription()
=======
>>>>>>> origin/gestionOffre
    public function setBudget(float $budget): self
    {
        $this->budget = $budget;
        return $this;
    }

    public function getDescription(): string
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> origin/gestionOffre
    {
        return $this->description;
    }

<<<<<<< HEAD
=======
<<<<<<< HEAD
    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getGoogle_calendar_event_id()
=======
>>>>>>> origin/gestionOffre
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getGoogleCalendarEventId(): string
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> origin/gestionOffre
    {
        return $this->google_calendar_event_id;
    }

<<<<<<< HEAD
=======
<<<<<<< HEAD
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
>>>>>>> origin/gestionOffre
    public function setGoogleCalendarEventId(string $googleCalendarEventId): self
    {
        $this->google_calendar_event_id = $googleCalendarEventId;
        return $this;
    }
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> origin/gestionOffre
}
