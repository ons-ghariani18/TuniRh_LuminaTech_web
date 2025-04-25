<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Offresemploi;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Candidatures
{
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private ?int $id_candidature = null;

    #[ORM\ManyToOne(targetEntity: Offresemploi::class, inversedBy: "candidaturess")]
    #[ORM\JoinColumn(name: 'id_offre', referencedColumnName: 'id_offre', onDelete: 'CASCADE')]
    #[Assert\NotNull(message: "L'offre associée est obligatoire")]
    private ?Offresemploi $offre = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le nom du candidat est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $nom_candidat = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "Veuillez fournir une adresse email valide")]
    private ?string $email = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $cv = null;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "La lettre de motivation est obligatoire")]
    #[Assert\Length(
        min: 10,
        minMessage: "La lettre de motivation doit contenir au moins {{ limit }} caractères"
    )]
    private ?string $lettre_motivation = null;

    #[ORM\Column(type: "date")]
    #[Assert\NotNull(message: "La date de candidature est obligatoire")]
    private ?\DateTimeInterface $date_candidature = null;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank(message: "Le statut est obligatoire")]
    #[Assert\Choice(
        choices: ["En attente", "Acceptée", "Rejetée"],
        message: "Le statut doit être En attente, Acceptée ou Rejetée"
    )]
    private ?string $statut = 'En attente';

    // Getters/Setters
    public function getIdCandidature(): ?int
    {
        return $this->id_candidature;
    }

    public function setIdCandidature(?int $value): self
    {
        $this->id_candidature = $value;
        return $this;
    }

    public function getOffre(): ?Offresemploi
    {
        return $this->offre;
    }

    public function setOffre(?Offresemploi $offre): self
    {
        $this->offre = $offre;
        return $this;
    }

    public function getNomCandidat(): ?string
    {
        return $this->nom_candidat;
    }

    public function setNomCandidat(?string $value): self
    {
        $this->nom_candidat = $value;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $value): self
    {
        $this->email = $value;
        return $this;
    }

    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(?string $value): self
    {
        $this->cv = $value;
        return $this;
    }

    public function getLettreMotivation(): ?string
    {
        return $this->lettre_motivation;
    }

    public function setLettreMotivation(?string $value): self
    {
        $this->lettre_motivation = $value;
        return $this;
    }

    public function getDateCandidature(): ?\DateTimeInterface
    {
        return $this->date_candidature;
    }

    public function setDateCandidature(?\DateTimeInterface $value): self
    {
        $this->date_candidature = $value;
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
}