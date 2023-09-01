<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\UniqueEmail;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Length(
        min: 2,
        max: 127,
        minMessage: 'Your name must be at least {{ limit }} characters long',
        maxMessage: 'Your name cannot be longer than {{ limit }} characters',
        groups: ['registration', 'update_user']
    )]
    #[Assert\NotBlank(groups: ['registration', 'update_user'])]
    #[ORM\Column(length: 127)]
    private ?string $full_name = null;

    #[Assert\Length(
        min: 4,
        max: 127,
        minMessage: 'Your email must be at least {{ limit }} characters long',
        maxMessage: 'Your email cannot be longer than {{ limit }} characters',
        groups: ['registration']
    )]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
        groups: ['registration']
    )]
    #[Assert\NotBlank(groups: ['registration'])]
    #[Assert\NotNull(groups: ['registration'])]
    #[UniqueEmail(groups: ['registration'])]
    #[ORM\Column(name: 'email', length: 127, unique: true, type: 'string')]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[Assert\Length(
        max: 15,
        maxMessage: 'Your house number cannot be longer than {{ limit }} characters',
        groups: ['update_user']
    )]
    #[ORM\Column(length: 15, nullable: true)]
    private ?string $house_number = null;

    #[Assert\Length(
        max: 255,
        maxMessage: 'Your street address cannot be longer than {{ limit }} characters',
        groups: ['update_user']
    )]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $street_address = null;

    #[Assert\Length(
        max: 127,
        maxMessage: 'Your city cannot be longer than {{ limit }} characters',
        groups: ['update_user']
    )]
    #[ORM\Column(length: 127, nullable: true)]
    private ?string $city = null;

    #[Assert\Length(
        max: 15,
        maxMessage: 'Your post code cannot be longer than {{ limit }} characters',
        groups: ['update_user']
    )]
    #[ORM\Column(length: 15, nullable: true)]
    private ?string $postcode = null;

    #[Assert\PasswordStrength([
        'minScore' => Assert\PasswordStrength::STRENGTH_MEDIUM,
        'message' => 'This password is not strong enough. Please choose a stronger password.',
        'groups' => ['registration', 'change_password']
    ])]
    #[Assert\NotBlank(groups: ['registration'])]
    #[Assert\Length(min: 6, max: 64, groups: ['registration', 'change_password'])]
    protected ?string $rawPassword = null;

    use TimestampableEntity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->full_name;
    }

    public function setFullName(?string $full_name = null): static
    {
        $this->full_name = $full_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function setRawPassword(?string $rawPassword): static
    {
        $this->rawPassword = $rawPassword;

        return $this;
    }

    public function getHouseNumber(): ?string
    {
        return $this->house_number;
    }

    public function setHouseNumber(?string $house_number): static
    {
        $this->house_number = $house_number;

        return $this;
    }

    public function getStreetAddress(): ?string
    {
        return $this->street_address;
    }

    public function setStreetAddress(?string $street_address): static
    {
        $this->street_address = $street_address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): static
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        $this->rawPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
