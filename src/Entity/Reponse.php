<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "reponse")]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", name: "id_reponse")]
    private ?int $idReponse = null;

    #[ORM\ManyToOne(targetEntity: Reclamation::class, inversedBy: "reponses")]
    #[ORM\JoinColumn(name: "id_reclam_rep", referencedColumnName: "id_reclamation", nullable: false, onDelete: "CASCADE")]
    private ?Reclamation $idReclamRep = null;

    #[ORM\Column(type: "text")]
    private ?string $message = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $dateReponse = null;

    public function __construct()
    {
        $this->dateReponse = new \DateTime();
    }

    public function getIdReponse(): ?int
    {
        return $this->idReponse;
    }

    public function getIdReclamRep(): ?Reclamation
    {
        return $this->idReclamRep;
    }

    public function setIdReclamRep(?Reclamation $idReclamRep): self
    {
        $this->idReclamRep = $idReclamRep;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getDateReponse(): ?\DateTimeInterface
    {
        return $this->dateReponse;
    }

    public function setDateReponse(?\DateTimeInterface $dateReponse): self
    {
        $this->dateReponse = $dateReponse;
        return $this;
    }
}