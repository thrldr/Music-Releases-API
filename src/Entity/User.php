<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
#[DoctrineAssert\UniqueEntity("email")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: 'Your password must be at least {{ limit }} characters long',
        maxMessage: 'Your password cannot be longer than {{ limit }} characters',
    )]
    private ?string $plainPassword = null;

    /**
     * a binary array of notification services
     * @see App\Services\Notifiers\Parser\UserNotificationServicesParser
     */
    #[ORM\Column]
    private int $notificationServices = 0;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\ManyToMany(targetEntity: Band::class, inversedBy: 'subscribedUsers')]
    #[Assert\Unique]
    private Collection $subscribedBands;

    public function __construct(
        string $email,
        string $plainPassword,
        int $notificationServices = 0,
    )
    {
        $this->email = $email;
        $this->plainPassword = $plainPassword;
        $this->notificationServices = $notificationServices;
        $this->subscribedBands = new ArrayCollection();
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
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

    /**
     * @return string the hashed password for this user
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getNotificationServices(): ?int
    {
        return $this->notificationServices;
    }

    public function setNotificationServices(int $notificationServices): self
    {
        $this->notificationServices = $notificationServices;

        return $this;
    }

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

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Band>
     */
    public function getSubscribedBands(): Collection
    {
        return $this->subscribedBands;
    }

    public function addSubscribedBand(Band $subscribedBand): self
    {
        if (!$this->subscribedBands->contains($subscribedBand)) {
            $this->subscribedBands->add($subscribedBand);
        }

        return $this;
    }

    public function removeSubscribedBand(Band $subscribedBand): self
    {
        $this->subscribedBands->removeElement($subscribedBand);

        return $this;
    }
}
