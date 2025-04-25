<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Candidatures;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Offresemploi
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    private ?int $id_offre = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $titre = null;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "La description est obligatoire")]
    #[Assert\Length(
        min: 10,
        minMessage: "La description doit contenir au moins {{ limit }} caractères"
    )]
    private ?string $description = null;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "Les compétences requises sont obligatoires")]
    #[Assert\Length(
        min: 5,
        minMessage: "Les compétences requises doivent contenir au moins {{ limit }} caractères"
    )]
    private ?string $competences_requises = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le lieu est obligatoire")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le lieu ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $lieu = null;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank(message: "Le type de contrat est obligatoire")]
    #[Assert\Choice(
        choices: ["CDI", "CDD", "Freelance"],
        message: "Le type de contrat doit être CDI, CDD ou Freelance"
    )]
    private ?string $type_contrat = null;

    #[ORM\Column(type: "date")]
    #[Assert\NotNull(message: "La date de publication est obligatoire")]
    #[Assert\LessThanOrEqual(
        value: "today",
        message: "La date de publication ne peut pas être dans le futur"
    )]
    private ?\DateTimeInterface $date_publication = null;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank(message: "Le statut est obligatoire")]
    #[Assert\Choice(
        choices: ["Ouverte", "Fermée", "En attente"],
        message: "Le statut doit être Ouverte, Fermée ou En attente"
    )]
    private ?string $statut = 'Ouverte';

    #[ORM\OneToMany(mappedBy: "id_offre", targetEntity: Candidatures::class)]
    private Collection $candidaturess;

    public function __construct()
    {
        $this->candidaturess = new ArrayCollection();
    }

    public function getIdOffre(): ?int
    {
        return $this->id_offre;
    }

    public function setIdOffre($value): self
    {
        $this->id_offre = $value;
        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $value): self
    {
        $this->titre = $value;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $value): self
    {
        $this->description = $value;
        return $this;
    }

    public function getCompetencesrequises(): ?string
    {
        return $this->competences_requises;
    }

    public function setCompetencesrequises(?string $value): self
    {
        $this->competences_requises = $value;
        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $value): self
    {
        $this->lieu = $value;
        return $this;
    }

    public function getTypecontrat(): ?string
    {
        return $this->type_contrat;
    }

    public function setTypecontrat(?string $value): self
    {
        $this->type_contrat = $value;
        return $this;
    }

    public function getDatepublication(): ?\DateTimeInterface
    {
        return $this->date_publication;
    }

    public function setDatepublication(?\DateTimeInterface $value): self
    {
        $this->date_publication = $value;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $value): self
    {
        $this->statut = $value;
        return $this;
    }

    public function getCandidaturess(): Collection
    {
        return $this->candidaturess;
    }

    public function addCandidatures(Candidatures $candidatures): self
    {
        if (!$this->candidaturess->contains($candidatures)) {
            $this->candidaturess[] = $candidatures;
            $candidatures->setIdOffre($this);
        }

        return $this;
    }

    public function removeCandidatures(Candidatures $candidatures): self
    {
        if ($this->candidaturess->removeElement($candidatures)) {
            if ($candidatures->getIdOffre() === $this) {
                $candidatures->setIdOffre(null);
            }
        }

        return $this;
    }
}