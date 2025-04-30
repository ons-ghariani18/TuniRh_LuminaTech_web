<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
#[ORM\Table(name: "reclamation")]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", name: "id_reclamation")]
    private ?int $idReclamation = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "reclamations")]
    #[ORM\JoinColumn(name: "idUser", referencedColumnName: "id_user", nullable: true, onDelete: "SET NULL")]
    private ?Users $idUser = null;

    #[ORM\Column(type: "string", length: 100)]
    private ?string $sujet = null;

    #[ORM\Column(type: "text")]
    private ?string $description = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $dateReclamation = null;

    #[ORM\Column(type: "string", length: 50)]
    private ?string $statut = null;

    #[ORM\OneToMany(mappedBy: "idReclamRep", targetEntity: Reponse::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $reponses;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
        $this->dateReclamation = new \DateTime();
    }

    public function getIdReclamation(): ?int
    {
        return $this->idReclamation;
    }

    public function getIdUser(): ?Users
    {
        return $this->idUser;
    }

    public function setIdUser(?Users $idUser): self
    {
        $this->idUser = $idUser;
        return $this;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(?string $sujet): self
    {
        $this->sujet = $sujet;
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

    public function getDateReclamation(): ?\DateTimeInterface
    {
        return $this->dateReclamation;
    }

    public function setDateReclamation(?\DateTimeInterface $dateReclamation): self
    {
        $this->dateReclamation = $dateReclamation;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses[] = $reponse;
            $reponse->setIdReclamRep($this);
        }
        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        if ($this->reponses->removeElement($reponse)) {
            if ($reponse->getIdReclamRep() === $this) {
                $reponse->setIdReclamRep(null);
            }
        }
        return $this;
    }
}