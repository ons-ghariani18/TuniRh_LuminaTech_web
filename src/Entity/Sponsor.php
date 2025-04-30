<?php

namespace App\Entity;

<<<<<<< HEAD
=======
use Doctrine\Common\Collections\ArrayCollection;
>>>>>>> origin/gestions-formations
use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Evenement;

#[ORM\Entity]
class Sponsor
{
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_sp;
=======
=======
>>>>>>> origin/gestionUserReclamation
=======
>>>>>>> origin/gestions-formations
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_sp", type: "integer")]
    private ?int $id_sp = null;

<<<<<<< HEAD
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
=======
>>>>>>> origin/gestions-formations

    #[ORM\Column(type: "string", length: 255)]
    private string $nom;

    #[ORM\Column(type: "string", length: 100)]
    private string $type;

    #[ORM\Column(type: "string", length: 15)]
    private string $telephone;

    #[ORM\Column(type: "string", length: 255)]
    private string $email;

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
    public function getId_sp()
    {
        return $this->id_sp;
    }

    public function setId_sp($value)
    {
        $this->id_sp = $value;
    }

=======
=======
>>>>>>> origin/gestionUserReclamation
=======
>>>>>>> origin/gestions-formations
    public function getIdSp(): int
{
    return $this->id_sp;
}

public function setIdSp(int $idSp): self
{
    $this->id_sp = $idSp;
    return $this;
}
<<<<<<< HEAD
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
=======
>>>>>>> origin/gestions-formations
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

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function setTelephone($value)
    {
        $this->telephone = $value;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($value)
    {
        $this->email = $value;
    }

    #[ORM\OneToMany(mappedBy: "id_sp", targetEntity: Evenement::class)]
    private Collection $evenements;

<<<<<<< HEAD
=======
    public function __construct()
    {
        $this->evenements = new ArrayCollection();
    }

>>>>>>> origin/gestions-formations
        public function getEvenements(): Collection
        {
            return $this->evenements;
        }
    
        public function addEvenement(Evenement $evenement): self
        {
            if (!$this->evenements->contains($evenement)) {
                $this->evenements[] = $evenement;
                $evenement->setId_sp($this);
            }
    
            return $this;
        }
    
        public function removeEvenement(Evenement $evenement): self
        {
            if ($this->evenements->removeElement($evenement)) {
                // set the owning side to null (unless already changed)
                if ($evenement->getId_sp() === $this) {
                    $evenement->setId_sp(null);
                }
            }
    
            return $this;
        }
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
}
=======
}
>>>>>>> origin/gestionOffre
=======
}
>>>>>>> origin/gestionUserReclamation
=======
}
>>>>>>> origin/gestions-formations
