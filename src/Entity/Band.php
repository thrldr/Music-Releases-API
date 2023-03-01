<?php

namespace App\Entity;

use App\Repository\BandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BandRepository::class)]
#[UniqueEntity(fields: ["name"])]
class Band
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("band")]
    private ?string $name = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Album $lastAlbum = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Album $previousAlbum = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'subscribedBands')]
    private Collection $subscribedUsers;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->subscribedUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastAlbum(): ?Album
    {
        return $this->lastAlbum;
    }

    public function setLastAlbum(?Album $lastAlbum): self
    {
        $this->lastAlbum = $lastAlbum;

        return $this;
    }

    public function updateLatestRelease(Album $album)
    {
        $this->previousAlbum = $this->lastAlbum;
        $this->lastAlbum = $album;

        foreach ($this->subscribedUsers as $subscriber) {
            $subscriber->setNotified(true);
        }
    }

    /**
     * @return Collection<int, User>
     */
    public function getSubscribedUsers(): Collection
    {
        return $this->subscribedUsers;
    }

    public function addSubscribedUser(User $subscribedUser): self
    {
        if (!$this->subscribedUsers->contains($subscribedUser)) {
            $this->subscribedUsers->add($subscribedUser);
            $subscribedUser->addSubscribedBand($this);
        }

        return $this;
    }

    public function removeSubscribedUser(User $subscribedUser): self
    {
        if ($this->subscribedUsers->removeElement($subscribedUser)) {
            $subscribedUser->removeSubscribedBand($this);
        }

        return $this;
    }
}
