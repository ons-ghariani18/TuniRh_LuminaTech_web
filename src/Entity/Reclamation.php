<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Users;
use Doctrine\Common\Collections\Collection;
use App\Entity\Reponse;

#[ORM\Entity]
class Reclamation
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $idReclamation;

        #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "reclamations")]
    #[ORM\JoinColumn(name: 'idUser', referencedColumnName: 'id_user', onDelete: 'CASCADE')]
    private Users $idUser;

    #[ORM\Column(type: "string", length: 255)]
    private string $sujet;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_reclamation;

    #[ORM\Column(type: "string", length: 50)]
    private string $statut;

    public function getIdReclamation()
    {
        return $this->idReclamation;
    }

    public function setIdReclamation($value)
    {
        $this->idReclamation = $value;
    }

    public function getIdUser()
    {
        return $this->idUser;
    }

    public function setIdUser($value)
    {
        $this->idUser = $value;
    }

    public function getSujet()
    {
        return $this->sujet;
    }

    public function setSujet($value)
    {
        $this->sujet = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getDate_reclamation()
    {
        return $this->date_reclamation;
    }

    public function setDate_reclamation($value)
    {
        $this->date_reclamation = $value;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($value)
    {
        $this->statut = $value;
    }

    #[ORM\OneToMany(mappedBy: "idReclamation", targetEntity: Reponse::class)]
    private Collection $reponses;

        public function getReponses(): Collection
        {
            return $this->reponses;
        }
    
        public function addReponse(Reponse $reponse): self
        {
            if (!$this->reponses->contains($reponse)) {
                $this->reponses[] = $reponse;
                $reponse->setIdReclamation($this);
            }
    
            return $this;
        }
    
        public function removeReponse(Reponse $reponse): self
        {
            if ($this->reponses->removeElement($reponse)) {
                // set the owning side to null (unless already changed)
                if ($reponse->getIdReclamation() === $this) {
                    $reponse->setIdReclamation(null);
                }
            }
    
            return $this;
        }
}
