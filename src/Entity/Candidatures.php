<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
<<<<<<< HEAD

use App\Entity\Offresemploi;
=======
use App\Entity\Offresemploi;
use Symfony\Component\Validator\Constraints as Assert;
>>>>>>> origin/gestionOffre

#[ORM\Entity]
class Candidatures
{
<<<<<<< HEAD

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_candidature;

        #[ORM\ManyToOne(targetEntity: Offresemploi::class, inversedBy: "candidaturess")]
    #[ORM\JoinColumn(name: 'id_offre', referencedColumnName: 'id_offre', onDelete: 'CASCADE')]
    private Offresemploi $id_offre;

    #[ORM\Column(type: "string", length: 100)]
    private string $nom_candidat;

    #[ORM\Column(type: "string", length: 100)]
    private string $email;

    #[ORM\Column(type: "text")]
    private string $cv;

    #[ORM\Column(type: "text")]
    private string $lettre_motivation;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_candidature;

    #[ORM\Column(type: "string", length: 50)]
    private string $statu;

    public function getId_candidature()
=======
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

    #[ORM\Column(name: "date_candidature", type: "date")]
    #[Assert\NotNull(message: "La date de candidature est obligatoire")]
    private ?\DateTimeInterface $dateCandidature = null;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank(message: "Le statut est obligatoire")]
    #[Assert\Choice(
        choices: ["En attente", "Acceptée", "Rejetée"],
        message: "Le statut doit être En attente, Acceptée ou Rejetée"
    )]
    private ?string $statut = 'En attente';

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $score = null;

    // Getters/Setters
    public function getIdCandidature(): ?int
>>>>>>> origin/gestionOffre
    {
        return $this->id_candidature;
    }

<<<<<<< HEAD
    public function setId_candidature($value)
    {
        $this->id_candidature = $value;
    }

    public function getId_offre()
    {
        return $this->id_offre;
    }

    public function setId_offre($value)
    {
        $this->id_offre = $value;
    }

    public function getNom_candidat()
=======
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
>>>>>>> origin/gestionOffre
    {
        return $this->nom_candidat;
    }

<<<<<<< HEAD
    public function setNom_candidat($value)
    {
        $this->nom_candidat = $value;
    }

    public function getEmail()
=======
    public function setNomCandidat(?string $value): self
    {
        $this->nom_candidat = $value;
        return $this;
    }

    public function getEmail(): ?string
>>>>>>> origin/gestionOffre
    {
        return $this->email;
    }

<<<<<<< HEAD
    public function setEmail($value)
    {
        $this->email = $value;
    }

    public function getCv()
=======
    public function setEmail(?string $value): self
    {
        $this->email = $value;
        return $this;
    }

    public function getCv(): ?string
>>>>>>> origin/gestionOffre
    {
        return $this->cv;
    }

<<<<<<< HEAD
    public function setCv($value)
    {
        $this->cv = $value;
    }

    public function getLettre_motivation()
=======
    public function setCv(?string $value): self
    {
        $this->cv = $value;
        return $this;
    }

    public function getLettreMotivation(): ?string
>>>>>>> origin/gestionOffre
    {
        return $this->lettre_motivation;
    }

<<<<<<< HEAD
    public function setLettre_motivation($value)
    {
        $this->lettre_motivation = $value;
    }

    public function getDate_candidature()
    {
        return $this->date_candidature;
    }

    public function setDate_candidature($value)
    {
        $this->date_candidature = $value;
    }

    public function getStatu()
    {
        return $this->statu;
    }

    public function setStatu($value)
    {
        $this->statu = $value;
    }
}
=======
    public function setLettreMotivation(?string $value): self
    {
        $this->lettre_motivation = $value;
        return $this;
    }

    public function getDateCandidature(): ?\DateTimeInterface
    {
        return $this->dateCandidature;
    }

    public function setDateCandidature(?\DateTimeInterface $value): self
    {
        $this->dateCandidature = $value;
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

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): self
    {
        $this->score = $score;
        return $this;
    }
}
>>>>>>> origin/gestionOffre
