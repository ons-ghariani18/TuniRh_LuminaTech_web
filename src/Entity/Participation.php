<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Users;

#[ORM\Entity]
class Participation
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_participation;

        #[ORM\ManyToOne(targetEntity: Evenement::class, inversedBy: "participations")]
    #[ORM\JoinColumn(name: 'id_ev', referencedColumnName: 'id_ev', onDelete: 'CASCADE')]
    private Evenement $id_ev;

        #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "participations")]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', onDelete: 'CASCADE')]
    private Users $id_user;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_participation;

    public function getId_participation()
    {
        return $this->id_participation;
    }

    public function setId_participation($value)
    {
        $this->id_participation = $value;
    }

    public function getId_ev()
    {
        return $this->id_ev;
    }

    public function setId_ev($value)
    {
        $this->id_ev = $value;
    }

    public function getId_user()
    {
        return $this->id_user;
    }

    public function setId_user($value)
    {
        $this->id_user = $value;
    }

    public function getDate_participation()
    {
        return $this->date_participation;
    }

    public function setDate_participation($value)
    {
        $this->date_participation = $value;
    }
}
