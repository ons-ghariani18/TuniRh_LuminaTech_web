<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Offresemploi;

#[ORM\Entity]
class Candidatures
{

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
    {
        return $this->id_candidature;
    }

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
    {
        return $this->nom_candidat;
    }

    public function setNom_candidat($value)
    {
        $this->nom_candidat = $value;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($value)
    {
        $this->email = $value;
    }

    public function getCv()
    {
        return $this->cv;
    }

    public function setCv($value)
    {
        $this->cv = $value;
    }

    public function getLettre_motivation()
    {
        return $this->lettre_motivation;
    }

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
