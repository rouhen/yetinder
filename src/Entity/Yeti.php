<?php

namespace App\Entity;

use App\Repository\YetiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: YetiRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Yeti
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0.0])]
    private ?float $height = null;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0.0])]
    private ?float $weight = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    private ?File $imageFile = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private ?int $votes = 0;

    #[ORM\Column(type: 'datetime', name: 'vote_timestamp', nullable: true)]
    private ?\DateTimeInterface $voteTimestamp = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $created = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $modified = null;

    public function __construct()
    {
        $this->created = new \DateTimeImmutable();
        $this->modified = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->modified = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getVotes(): ?int
    {
        return $this->votes;
    }

    public function setVotes(?int $votes): self
    {
        $this->votes = $votes;
        $this->voteTimestamp = new \DateTime();

        return $this;
    }

    public function getVoteTimestamp(): ?\DateTimeInterface
    {
        return $this->voteTimestamp;
    }

    public function getCreated(): ?\DateTimeImmutable
    {
        return $this->created;
    }

    public function getModified(): ?\DateTimeInterface
    {
        return $this->modified;
    }
}
