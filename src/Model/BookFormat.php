<?php

declare(strict_types=1);

namespace App\Model;

class BookFormat
{
    private int $id;

    private string $title;

    private ?string $description;

    private ?string $comment;

    private float $price;

    private ?int $dicountPercent;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDicountPercent(): ?int
    {
        return $this->dicountPercent;
    }

    public function setDicountPercent(?int $dicountPercent): self
    {
        $this->dicountPercent = $dicountPercent;

        return $this;
    }
}
