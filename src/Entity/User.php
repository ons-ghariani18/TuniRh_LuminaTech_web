<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const TYPE_RH = 'rh';
    public const TYPE_EMPLOYEE = 'employee';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 20)]
    private string $type = self::TYPE_EMPLOYEE; // Default value moved here for clarity

    public function __construct()
    {
        // Constructor can be empty since we set the default value in the property definition
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        
        // Add ROLE_USER if not present
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }
        
        // Add specific role based on type
        if ($this->type === self::TYPE_RH) {
            $roles[] = 'ROLE_RH';
        } else {
            $roles[] = 'ROLE_EMPLOYEE';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        if (!in_array($type, [self::TYPE_RH, self::TYPE_EMPLOYEE])) {
            throw new \InvalidArgumentException("Invalid user type");
        }

        $this->type = $type;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // Clear any sensitive temporary data
    }

    public function isRh(): bool
    {
        return $this->type === self::TYPE_RH;
    }

    public function isEmployee(): bool
    {
        return $this->type === self::TYPE_EMPLOYEE;
    }
}