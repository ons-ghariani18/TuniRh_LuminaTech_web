<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Sponsor;
use Doctrine\Common\Collections\Collection;
use App\Entity\Participation;

#[ORM\Entity]
class Evenement
{

   
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_ev", type: "integer")]
    private ?int $id_ev = null;


    #[ORM\ManyToOne(targetEntity: Sponsor::class, inversedBy: "evenements")]
    #[ORM\JoinColumn(name: 'id_sp', referencedColumnName: 'id_sp', onDelete: 'CASCADE')]
    private Sponsor $id_sp;

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


 

    public function getId_ev()
    {
        return $this->id_ev;
    }

    public function setId_ev($value)
    {
        $this->id_ev = $value;
    }

    public function getIdSp(): Sponsor
    {
        return $this->id_sp;
    }
    
    public function setIdSp(Sponsor $idSp): self
    {
        $this->id_sp = $id_sp;
        return $this;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($value)
    {
        $this->nom = $value;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($value)
    {
        $this->type = $value;
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

    public function getLieu()
    {
        return $this->lieu;
    }

    public function setLieu($value)
    {
        $this->lieu = $value;
    }

    public function getBudget()
    {
        return $this->budget;
    }

    public function setBudget($value)
    {
        $this->budget = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
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
}
