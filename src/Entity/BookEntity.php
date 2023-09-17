<?php

namespace App\Entity;

use App\Repository\BookEntityRepository;
use Doctrine\ORM\Mapping as ORM;

// Назвал BookEntity, для того чтобы не было конфликта, т.к Book уже есть на моем компьютере
#[ORM\Entity(repositoryClass: BookEntityRepository::class)]
class BookEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column]
    private ?int $amountOfPages = null;

    #[ORM\Column]
    private ?string $isbn = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getAmountOfPages(): ?int
    {
        return $this->amountOfPages;
    }

    public function setAmountOfPages(int $amountOfPages): static
    {
        $this->amountOfPages = $amountOfPages;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }
}
