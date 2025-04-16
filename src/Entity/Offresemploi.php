<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Candidatures;

#[ORM\Entity]
class Offresemploi
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_offre;

    #[ORM\Column(type: "string", length: 255)]
    private string $titre;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "text")]
    private string $competences_requises;

    #[ORM\Column(type: "string", length: 255)]
    private string $lieu;

    #[ORM\Column(type: "string", length: 50)]
    private string $type_contrat;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_publication;

    #[ORM\Column(type: "string", length: 50)]
    private string $statut;

    public function getId_offre()
    {
        return $this->id_offre;
    }

    public function setId_offre($value)
    {
        $this->id_offre = $value;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setTitre($value)
    {
        $this->titre = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getCompetences_requises()
    {
        return $this->competences_requises;
    }

    public function setCompetences_requises($value)
    {
        $this->competences_requises = $value;
    }

    public function getLieu()
    {
        return $this->lieu;
    }

    public function setLieu($value)
    {
        $this->lieu = $value;
    }

    public function getType_contrat()
    {
        return $this->type_contrat;
    }

    public function setType_contrat($value)
    {
        $this->type_contrat = $value;
    }

    public function getDate_publication()
    {
        return $this->date_publication;
    }

    public function setDate_publication($value)
    {
        $this->date_publication = $value;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($value)
    {
        $this->statut = $value;
    }

    #[ORM\OneToMany(mappedBy: "id_offre", targetEntity: Candidatures::class)]
    private Collection $candidaturess;

    public function __construct()
    {
        $this->candidaturess = new ArrayCollection();
    }

        public function getCandidaturess(): Collection
        {
            return $this->candidaturess;
        }
    
        public function addCandidatures(Candidatures $candidatures): self
        {
            if (!$this->candidaturess->contains($candidatures)) {
                $this->candidaturess[] = $candidatures;
                $candidatures->setId_offre($this);
            }
    
            return $this;
        }
    
        public function removeCandidatures(Candidatures $candidatures): self
        {
            if ($this->candidaturess->removeElement($candidatures)) {
                // set the owning side to null (unless already changed)
                if ($candidatures->getId_offre() === $this) {
                    $candidatures->setId_offre(null);
                }
            }
    
            return $this;
        }

        public function getIdOffre(): ?int
        {
            return $this->id_offre;
        }

        public function getCompetencesRequises(): ?string
        {
            return $this->competences_requises;
        }

        public function setCompetencesRequises(string $competences_requises): static
        {
            $this->competences_requises = $competences_requises;

            return $this;
        }

        public function getTypeContrat(): ?string
        {
            return $this->type_contrat;
        }

        public function setTypeContrat(string $type_contrat): static
        {
            $this->type_contrat = $type_contrat;

            return $this;
        }

        public function getDatePublication(): ?\DateTimeInterface
        {
            return $this->date_publication;
        }

        public function setDatePublication(\DateTimeInterface $date_publication): static
        {
            $this->date_publication = $date_publication;

            return $this;
        }

        public function addCandidaturess(Candidatures $candidaturess): static
        {
            if (!$this->candidaturess->contains($candidaturess)) {
                $this->candidaturess->add($candidaturess);
                $candidaturess->setIdOffre($this);
            }

            return $this;
        }

        public function removeCandidaturess(Candidatures $candidaturess): static
        {
            if ($this->candidaturess->removeElement($candidaturess)) {
                // set the owning side to null (unless already changed)
                if ($candidaturess->getIdOffre() === $this) {
                    $candidaturess->setIdOffre(null);
                }
            }

            return $this;
        }
}
