<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Entity\Evenement;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[UniqueEntity(fields: ['nom'], message: 'Un sponsor avec ce nom existe déjà.')]
class Sponsor {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_sp", type: "integer")]
    private ?int $id_sp = null;
    
    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le nom du sponsor ne peut pas être vide")]
    #[Assert\Length(
        min: 2,
        max: 10,
        minMessage: "Le nom du sponsor doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom du sponsor ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $nom;
    
    #[ORM\Column(type: "string", length: 100)]
    #[Assert\NotBlank(message: "Le type du sponsor ne peut pas être vide")]
    #[Assert\Choice(
        choices: ["Financier", "Matériel", "Services", "Médiatique"],
        message: "Choisissez un type de sponsor valide"
    )]
    private string $type;
    
    #[ORM\Column(type: "string", length: 15)]
    #[Assert\NotBlank(message: "Le numéro de téléphone ne peut pas être vide")]
    #[Assert\Regex(
        pattern: "/^[0-9]{8,10}$/",
        message: "Le numéro de téléphone doit contenir entre 8 et 10 chiffres"
    )]
    private string $telephone;
    
    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "L'email ne peut pas être vide.")]
    #[Assert\Email(
        mode: "strict", // ✅ stricte : pas de tolérance sur les formats approximatifs
        message: "Veuillez vérifier la saisie de l'email : '{{ value }}' n'est pas valide."
    )]
    private string $email;

    
    #[ORM\OneToMany(mappedBy: "id_sp", targetEntity: Evenement::class)]
    private Collection $evenements;
    
    public function __construct()
    {
        $this->evenements = new ArrayCollection();
    }
    
    public function getIdSp(): ?int
    {
        return $this->id_sp;
    }
    
    public function setIdSp(int $idSp): self
    {
        $this->id_sp = $idSp;
        return $this;
    }
    
    public function getNom()
    {
        return $this->nom;
    }
    
    public function setNom($value)
    {
        $this->nom = $value;
        return $this;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function setType($value)
    {
        $this->type = $value;
        return $this;
    }
    
    public function getTelephone()
    {
        return $this->telephone;
    }
    
    public function setTelephone($value)
    {
        $this->telephone = $value;
        return $this;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function setEmail($value)
    {
        $this->email = $value;
        return $this;
    }
    
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
}