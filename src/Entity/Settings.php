<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nameSelector = null;

    #[ORM\Column(nullable: true)]
    private ?int $amountBookPagination = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adminEmail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sourceJson = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameSelector(): ?string
    {
        return $this->nameSelector;
    }

    public function setNameSelector(?string $nameSelector): static
    {
        $this->nameSelector = $nameSelector;

        return $this;
    }

    public function getAmountBookPagination(): ?int
    {
        return $this->amountBookPagination;
    }

    public function setAmountBookPagination(?int $amountBookPagination): static
    {
        $this->amountBookPagination = $amountBookPagination;

        return $this;
    }

    public function getAdminEmail(): ?string
    {
        return $this->adminEmail;
    }

    public function setAdminEmail(?string $adminEmail): static
    {
        $this->adminEmail = $adminEmail;

        return $this;
    }

    public function getSourceJson(): ?string
    {
        return $this->sourceJson;
    }

    public function setSourceJson(?string $sourceJson): static
    {
        $this->sourceJson = $sourceJson;

        return $this;
    }
}
