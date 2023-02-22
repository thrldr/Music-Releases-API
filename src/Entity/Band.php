<?php

namespace App\Entity;

use App\Repository\BandRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: BandRepository::class)]
#[UniqueEntity(fields: ["name"])]
class Band
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Album $lastAlbum = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Album $previousAlbum = null;

    public function __construct(string $name)
    {
        $this->name = $name;
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

    public function getPreviousAlbum(): ?Album
    {
        return $this->previousAlbum;
    }

    public function setPreviousAlbum(?Album $previousAlbum): self
    {
        $this->previousAlbum = $previousAlbum;

        return $this;
    }

    public function updateLatestRelease(Album $album)
    {
        $this->previousAlbum = $this->lastAlbum;
        $this->lastAlbum = $album;
    }
}
