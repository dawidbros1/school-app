<?php

namespace App\Entity;

use App\Interfaces\CustomUserInterface as UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

abstract class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=true)
     * @Assert\Length(min=6, minMessage="email musi mieć co najmniej {{ limit }} znaków")
     * @Assert\Length(max=36, maxMessage="email nie może mieć więcej niż {{ limit }} znaków")
     */
    protected $email;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string" , nullable=true)
     */

    protected $password;

    /**
     * @ORM\Column(type="json")
     */
    protected $roles = [];

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="To pole nie może być puste")
     * @Assert\Length(max=36, maxMessage="Imię nie może mieć więcej niż {{ limit }} znaków")
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="To pole nie może być puste")
     * @Assert\Length(max=36, maxMessage="Nazwisko nie może mieć więcej niż {{ limit }} znaków") 
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", length=11)
     * @Assert\Length(min=11, max=11)
     * @Assert\Regex(pattern="/^\d+$/", message="Pesel może zawierać tylko cyfry")
     */
    protected $pesel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $code;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }
    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getPesel()
    {
        return $this->pesel;
    }

    public function setPesel($pesel)
    {
        $this->pesel = $pesel;

        return $this;
    }

    public function isActive()
    {
        return ($this->code == null);
    }

    public function name()
    {
        return $this->lastName . " " . $this->firstName;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}