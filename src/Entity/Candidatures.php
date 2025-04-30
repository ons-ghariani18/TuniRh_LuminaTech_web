<?php

namespace App\Entity;

<<<<<<< HEAD
use Doctrine\ORM\Mapping as ORM;
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
use App\Entity\Offresemploi;
use Symfony\Component\Validator\Constraints as Assert;
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421

use App\Entity\Offresemploi;
=======
use App\Entity\Offresemploi;
use Symfony\Component\Validator\Constraints as Assert;
>>>>>>> origin/gestionOffre
<<<<<<< HEAD
=======
use App\Entity\Offresemploi;
use Symfony\Component\Validator\Constraints as Assert;
>>>>>>> origin/gestionUserReclamation
=======
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\Offresemploi;
>>>>>>> origin/gestions-formations
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421

#[ORM\Entity]
class Candidatures
{
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> origin/gestions-formations
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421

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
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
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

<<<<<<< HEAD
    #[ORM\Column(name: "date_candidature", type: "date")]
    #[Assert\NotNull(message: "La date de candidature est obligatoire")]
    private ?\DateTimeInterface $dateCandidature = null;
=======
    #[ORM\Column(type: "date")]
    #[Assert\NotNull(message: "La date de candidature est obligatoire")]
    private ?\DateTimeInterface $date_candidature = null;
>>>>>>> origin/gestionUserReclamation

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank(message: "Le statut est obligatoire")]
    #[Assert\Choice(
        choices: ["En attente", "Acceptée", "Rejetée"],
        message: "Le statut doit être En attente, Acceptée ou Rejetée"
    )]
    private ?string $statut = 'En attente';

<<<<<<< HEAD
    #[ORM\Column(type: "float", nullable: true)]
    private ?float $score = null;

    // Getters/Setters
    public function getIdCandidature(): ?int
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> origin/gestionOffre
=======
    // Getters/Setters
    public function getIdCandidature(): ?int
>>>>>>> origin/gestionUserReclamation
=======
>>>>>>> origin/gestions-formations
    {
        return $this->id_candidature;
    }

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> origin/gestions-formations
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
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
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
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
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
=======
>>>>>>> origin/gestions-formations
    {
        return $this->nom_candidat;
    }

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> origin/gestions-formations
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setNom_candidat($value)
    {
        $this->nom_candidat = $value;
    }

    public function getEmail()
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setNomCandidat(?string $value): self
    {
        $this->nom_candidat = $value;
        return $this;
    }

    public function getEmail(): ?string
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
=======
>>>>>>> origin/gestions-formations
    {
        return $this->email;
    }

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> origin/gestions-formations
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setEmail($value)
    {
        $this->email = $value;
    }

    public function getCv()
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setEmail(?string $value): self
    {
        $this->email = $value;
        return $this;
    }

    public function getCv(): ?string
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
=======
>>>>>>> origin/gestions-formations
    {
        return $this->cv;
    }

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> origin/gestions-formations
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setCv($value)
    {
        $this->cv = $value;
    }

    public function getLettre_motivation()
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setCv(?string $value): self
    {
        $this->cv = $value;
        return $this;
    }

    public function getLettreMotivation(): ?string
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
=======
>>>>>>> origin/gestions-formations
    {
        return $this->lettre_motivation;
    }

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> origin/gestions-formations
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
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
<<<<<<< HEAD
}
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
    public function setLettreMotivation(?string $value): self
    {
        $this->lettre_motivation = $value;
=======

    public function getIdCandidature(): ?int
    {
        return $this->id_candidature;
    }

    public function getNomCandidat(): ?string
    {
        return $this->nom_candidat;
    }

    public function setNomCandidat(string $nom_candidat): static
    {
        $this->nom_candidat = $nom_candidat;

        return $this;
    }

    public function getLettreMotivation(): ?string
    {
        return $this->lettre_motivation;
    }

    public function setLettreMotivation(string $lettre_motivation): static
    {
        $this->lettre_motivation = $lettre_motivation;

>>>>>>> origin/gestions-formations
        return $this;
    }

    public function getDateCandidature(): ?\DateTimeInterface
    {
<<<<<<< HEAD
<<<<<<< HEAD
        return $this->dateCandidature;
=======
        return $this->date_candidature;
>>>>>>> origin/gestionUserReclamation
    }

    public function setDateCandidature(?\DateTimeInterface $value): self
    {
<<<<<<< HEAD
        $this->dateCandidature = $value;
=======
        $this->date_candidature = $value;
>>>>>>> origin/gestionUserReclamation
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
<<<<<<< HEAD

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): self
    {
        $this->score = $score;
        return $this;
    }
<<<<<<< HEAD
<<<<<<< HEAD
}
=======
}
>>>>>>> origin/gestionOffre
<<<<<<< HEAD
=======
}
>>>>>>> origin/gestionUserReclamation
=======
        return $this->date_candidature;
    }

    public function setDateCandidature(\DateTimeInterface $date_candidature): static
    {
        $this->date_candidature = $date_candidature;

        return $this;
    }

    public function getIdOffre(): ?Offresemploi
    {
        return $this->id_offre;
    }

    public function setIdOffre(?Offresemploi $id_offre): static
    {
        $this->id_offre = $id_offre;

        return $this;
    }
}
>>>>>>> origin/gestions-formations
=======
>>>>>>> origin/gestionOffre
=======
}
>>>>>>> origin/gestionOffre
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
