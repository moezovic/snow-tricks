<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email",
 * message ="l'adresse email est deja utilisé"
 *)
 */
class User implements UserInterface, \Serializable 
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank(
            message = "La valeur ne peut pas être vide."
     *)
     * @Assert\Email(
     *     message = "La valeur de votre émail '{{ value }}' n'est pas correcte.",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(
            message = "La valeur ne peut pas être vide."
     *)
     * @Assert\Length(
     *      min = 8,
     *      max = 50,
     *      minMessage = "Votre mot de passe doit contenir au moins 8 caractéres",
     *      maxMessage = "Votre mot de passe doit contenir au moins 50 caractéres"
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(
            message = "La valeur ne peut pas être vide."
     *)
     * @Assert\Length(
     *      min = 8,
     *      max = 50,
     *      minMessage = "Votre nom d'utilisateur doit contenir au moins 8 caractéres",
     *      maxMessage = "Votre nom d'utilisateur doit contenir au moins 50 caractéres"
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    public function __construct()
    {
        $this->setIsActive(true);
    }

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {

    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
        ) = unserialize($serialized, array('allowed_classes' => false));
    }

}


