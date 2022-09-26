<?php

declare(strict_types=1);

namespace App\Entity;

use JsonSerializable;

use Symfony\Component\Validator\Constraints as Assert;

class Ingredient implements JsonSerializable
{
    #[Assert\NotBlank]
    private string $name;

    #[Assert\NotBlank]
    private string $quantity;

    #[Assert\NotBlank]
    private string $unit;

    public function __construct(
        string $name = '',
        string $quantity = '',
        string $unit = ''
    ) {
        $this->name = $name;
        $this->quantity = $quantity;
        $this->unit = $unit;
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

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name . ' ' . $this->quantity . $this->unit;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
        ];
    }
}
