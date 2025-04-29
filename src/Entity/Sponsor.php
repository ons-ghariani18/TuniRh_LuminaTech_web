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
class Sponsor 
{
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
        mode: "strict",
        message: "Veuillez vérifier la saisie de l'email : '{{ value }}' n'est pas valide."
    )]
    private string $email;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $logoFilename = null;
    
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
    
    public function getNom(): string
    {
        return $this->nom;
    }
    
    public function setNom(string $value): self
    {
        $this->nom = $value;
        return $this;
    }
    
    public function getType(): string
    {
        return $this->type;
    }
    
    public function setType(string $value): self
    {
        $this->type = $value;
        return $this;
    }
    
    public function getTelephone(): string
    {
        return $this->telephone;
    }
    
    public function setTelephone(string $value): self
    {
        $this->telephone = $value;
        return $this;
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function setEmail(string $value): self
    {
        $this->email = $value;
        return $this;
    }
    
    public function getLogoFilename(): ?string
    {
        return $this->logoFilename;
    }
    
    public function setLogoFilename(?string $logoFilename): self
    {
        $this->logoFilename = $logoFilename;
        return $this;
    }
    
    public function hasLogo(): bool
    {
        return $this->logoFilename !== null && $this->logoFilename !== '';
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
            if ($evenement->getId_sp() === $this) {
                $evenement->setId_sp(null);
            }
        }
        
        return $this;
    }
    
    // Méthode pour faciliter l'affichage de l'objet
    public function __toString(): string
    {
        return $this->nom;
    }
}