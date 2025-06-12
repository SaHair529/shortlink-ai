<?php

namespace App\Entity;

use App\Repository\ShortLinkRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ShortLinkRepository::class)]
class ShortLink
{
    #[Groups(['shortLink'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['shortLink'])]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $shortLink = null;

    #[Groups(['shortLink'])]
    #[ORM\Column(length: 255)]
    private ?string $originalLink = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShortLink(): ?string
    {
        return $this->shortLink;
    }

    public function setShortLink(string $shortLink): static
    {
        $this->shortLink = $shortLink;

        return $this;
    }

    public function getOriginalLink(): ?string
    {
        return $this->originalLink;
    }

    public function setOriginalLink(string $originalLink): static
    {
        $this->originalLink = $originalLink;

        return $this;
    }
}
