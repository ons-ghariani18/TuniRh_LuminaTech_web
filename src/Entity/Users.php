<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD

use Doctrine\Common\Collections\Collection;
use App\Entity\Reservations;

#[ORM\Entity]
class Users
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_user;

    #[ORM\Column(type: "string", length: 100)]
    private string $nom;

    #[ORM\Column(type: "string", length: 100)]
    private string $prenom;

    #[ORM\Column(type: "string", length: 255)]
    private string $email;

    #[ORM\Column(type: "string", length: 15)]
    private string $tel;

    #[ORM\Column(type: "string", length: 255)]
    private string $address;

    #[ORM\Column(type: "string", length: 255)]
    private string $password;

    #[ORM\Column(type: "string", length: 50)]
    private string $role;

    public function getId_user()
=======
=======
>>>>>>> origin/gestionUserReclamation
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Reclamation;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id_user = null;

    #[ORM\Column(type: "string", length: 100)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $nom;

    #[ORM\Column(type: "string", length: 100)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Le prénom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $prenom;

    #[ORM\Column(type: "string", length: 100, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "Veuillez fournir une adresse email valide")]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire")]
    #[Assert\Length(
        min: 6,
        minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères"
    )]
    private string $password;

    #[ORM\Column(type: "string", length: 20)]
    #[Assert\NotBlank(message: "Le téléphone est obligatoire")]
    #[Assert\Regex(
        pattern: "/^\+?[1-9]\d{1,14}$/",
        message: "Veuillez fournir un numéro de téléphone valide (ex. +21612345678)"
    )]
    private string $tel;

    #[ORM\Column(type: "string", length: 100)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire")]
    #[Assert\Length(
        min: 5,
        max: 100,
        minMessage: "L'adresse doit contenir au moins {{ limit }} caractères",
        maxMessage: "L'adresse ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $address;

    #[ORM\Column(type: "string", columnDefinition: "ENUM('Responsable RH', 'Employé', 'candidat')")]
    #[Assert\NotBlank(message: "Le rôle est obligatoire")]
    #[Assert\Choice(
        choices: ["Responsable RH", "Employé", "candidat"],
        message: "Le rôle doit être Responsable RH, Employé ou candidat"
    )]
    private string $role;

<<<<<<< HEAD
=======
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $avatar;

>>>>>>> origin/gestionUserReclamation
    #[ORM\OneToMany(mappedBy: "idUser", targetEntity: Reclamation::class)]
    private Collection $reclamations;

    public function __construct()
    {
        $this->reclamations = new ArrayCollection();
    }

    public function getIdUser(): ?int
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
    {
        return $this->id_user;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    public function setId_user($value)
    {
        $this->id_user = $value;
    }

    public function getNom()
=======
    public function getNom(): ?string
>>>>>>> origin/gestionOffre
=======
    public function getNom(): ?string
>>>>>>> origin/gestionUserReclamation
    {
        return $this->nom;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    public function setNom($value)
    {
        $this->nom = $value;
    }

    public function getPrenom()
=======
=======
>>>>>>> origin/gestionUserReclamation
    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
    {
        return $this->prenom;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    public function setPrenom($value)
    {
        $this->prenom = $value;
    }

    public function getEmail()
=======
=======
>>>>>>> origin/gestionUserReclamation
    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
=======
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Reservations;

#[ORM\Entity(repositoryClass: 'App\Repository\UsersRepository')]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $password;

    private ?string $plainPassword = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $accountType = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reservations::class, orphanRemoval: true)]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->roles = ['ROLE_USER']; // Rôle par défaut
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
>>>>>>> origin/gestions-formations
    {
        return $this->email;
    }

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
    public function setEmail($value)
    {
        $this->email = $value;
    }

    public function getTel()
    {
        return $this->tel;
    }

    public function setTel($value)
    {
        $this->tel = $value;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($value)
    {
        $this->address = $value;
    }

    public function getPassword()
=======
=======
>>>>>>> origin/gestionUserReclamation
=======
>>>>>>> origin/gestions-formations
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

<<<<<<< HEAD
    public function getPassword(): ?string
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
=======
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getPassword(): string
>>>>>>> origin/gestions-formations
    {
        return $this->password;
    }

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
    public function setPassword($value)
    {
        $this->password = $value;
    }

    public function getRole()
=======
=======
>>>>>>> origin/gestionUserReclamation
=======
>>>>>>> origin/gestions-formations
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

<<<<<<< HEAD
    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getRole(): ?string
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
    {
        return $this->role;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    public function setRole($value)
    {
        $this->role = $value;
    }

    #[ORM\OneToMany(mappedBy: "id_user", targetEntity: Participation::class)]
    private Collection $participations;

        public function getParticipations(): Collection
        {
            return $this->participations;
        }
    
        public function addParticipation(Participation $participation): self
        {
            if (!$this->participations->contains($participation)) {
                $this->participations[] = $participation;
                $participation->setId_user($this);
            }
    
            return $this;
        }
    
        public function removeParticipation(Participation $participation): self
        {
            if ($this->participations->removeElement($participation)) {
                // set the owning side to null (unless already changed)
                if ($participation->getId_user() === $this) {
                    $participation->setId_user(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "user_id", targetEntity: Reservations::class)]
    private Collection $reservationss;

        public function getReservationss(): Collection
        {
            return $this->reservationss;
        }
    
        public function addReservations(Reservations $reservations): self
        {
            if (!$this->reservationss->contains($reservations)) {
                $this->reservationss[] = $reservations;
                $reservations->setUser_id($this);
            }
    
            return $this;
        }
    
        public function removeReservations(Reservations $reservations): self
        {
            if ($this->reservationss->removeElement($reservations)) {
                // set the owning side to null (unless already changed)
                if ($reservations->getUser_id() === $this) {
                    $reservations->setUser_id(null);
                }
            }
    
            return $this;
        }
}
=======
=======
>>>>>>> origin/gestionUserReclamation
    public function setRole(string $role): self
    {
        if (!in_array($role, ['Responsable RH', 'Employé', 'candidat'])) {
            throw new \InvalidArgumentException("Rôle invalide. Les valeurs autorisées sont : 'Responsable RH', 'Employé', 'candidat'.");
        }
        $this->role = $role;
        return $this;
    }

    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations[] = $reclamation;
            $reclamation->setIdUser($this);
        }
        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        if ($this->reclamations->removeElement($reclamation)) {
            if ($reclamation->getIdUser() === $this) {
                $reclamation->setIdUser(null);
            }
        }
        return $this;
    }

    // Méthodes pour UserInterface
    public function getRoles(): array
    {
        $roleMap = [
            'Responsable RH' => 'ROLE_RH',
            'Employé' => 'ROLE_EMPLOYE',
            'candidat' => 'ROLE_CANDIDAT',
        ];
        return [$roleMap[$this->role] ?? 'ROLE_USER'];
=======
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Garantit que chaque utilisateur a ROLE_USER
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }
        
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getSalt(): ?string
    {
        return null; // Pas nécessaire quand on utilise bcrypt/argon
>>>>>>> origin/gestions-formations
    }

    public function eraseCredentials(): void
    {
<<<<<<< HEAD
        // Pas de données temporaires à effacer
=======
        $this->plainPassword = null;
>>>>>>> origin/gestions-formations
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
<<<<<<< HEAD
<<<<<<< HEAD
}
>>>>>>> origin/gestionOffre
=======

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;
        return $this;
    }
}
>>>>>>> origin/gestionUserReclamation
=======

    // Méthode pour compatibilité avec anciennes versions Symfony
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    public function setAccountType(?string $accountType): self
    {
        $this->accountType = $accountType;
        return $this;
    }

    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservations $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setUser($this);
        }
        return $this;
    }

    public function removeReservation(Reservations $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getUser() === $this) {
                $reservation->setUser(null);
            }
        }
        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }

    /**
     * Get the full name of the user or return email if no name is available
     */
    public function getFullName(): string
    {
        return $this->email; // Using email as fallback since there is no first/last name fields
    }
}
>>>>>>> origin/gestions-formations
