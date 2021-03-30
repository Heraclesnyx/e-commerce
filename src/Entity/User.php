<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     * @Assert\NotBlank()
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];


    ////Le pattern représente 1 Majuscule obligatoire,1 minuscule obligatoire et 1 caractère spéciale obligatoire, les chiffres sont facultatives ici.
    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=8,
     *     minMessage="Votre mot de passe doit contenir 8 caractères minimum."
     * )
     * @Assert\Regex(
     *     pattern="/^\S*(?=.*[A-Z])(?=.*[a-z])(?=.*[@#$%\\\[\]\(\)\{\}])\S*$/",
     *     message="Votre mot de passe doit contenir 1 majuscule, 1 minuscule et 1 caractère spéciale"
     * )
     */
    private $plainPassword;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min="3",
     *     minMessage="Vous devez avoir 3 lettres minimum pour votre prénom",
     *     max=60,
     *     maxMessage="Déconnez pas votre prénom ne peux pas excéder 60 caractères."
     * )
     *
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min="3",
     *     minMessage="Vous devez avoir 3 lettres minimum pour le nom",
     *     max=60,
     *     maxMessage="Houla votre nom dépasse les 60 caractères."
     * )
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @ORM\Column(type="integer")
     */
    private $attemptLogin;

    /**
     * @ORM\Column(type="integer")
     */
    private $compteur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getAttemptLogin(): ?int
    {
        return $this->attemptLogin;
    }

    public function setAttemptLogin(int $attemptLogin): self
    {
        $this->attemptLogin = $attemptLogin;

        return $this;
    }

    public function getCompteur(): ?int
    {
        return $this->compteur;
    }

    public function setCompteur(int $compteur): self
    {
        $this->compteur = $compteur;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
