<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $cooker = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recipe $recipe = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    private bool $validated = true;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    private bool $reported = false;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    private bool $modified = false;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCooker(): ?User
    {
        return $this->cooker;
    }

    public function setCooker(?User $cooker): self
    {
        $this->cooker = $cooker;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function isValidated(): bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): self
    {
        $this->validated = $validated;

        return $this;
    }

    public function isReported(): bool
    {
        return $this->reported;
    }

    public function setReported(bool $reported): self
    {
        $this->reported = $reported;

        return $this;
    }

    public function isModified(): bool
    {
        return $this->modified;
    }

    public function __toString(): string
    {
        return 'isReported: ' . $this->reported . ' - isModified: ' . $this->modified;
    }
}
