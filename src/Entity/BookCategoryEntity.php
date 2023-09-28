<?php

namespace App\Entity;

use App\Repository\BookCategoryEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookCategoryEntityRepository::class)]
class BookCategoryEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToMany(targetEntity: BookEntity::class, inversedBy: 'categories')]
    private Collection $books;

    #[ORM\Column]
    private ?int $level = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'categoryChild')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?self $parentCategory = null;

    #[ORM\OneToMany(mappedBy: 'parentCategory', targetEntity: self::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $categoryChild;



    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->categoryChild = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, BookEntity>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(BookEntity $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
        }

        return $this;
    }

    public function removeBook(BookEntity $book): static
    {
        $this->books->removeElement($book);

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getParentCategory(): ?self
    {
        return $this->parentCategory;
    }

    public function setParentCategory(?self $parentCategory): static
    {
        $this->parentCategory = $parentCategory;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getCategoryChild(): Collection
    {
        return $this->categoryChild;
    }

    public function addCategoryChild(self $categoryChild): static
    {
        if (!$this->categoryChild->contains($categoryChild)) {
            $this->categoryChild->add($categoryChild);
            $categoryChild->setParentCategory($this);
        }

        return $this;
    }

    public function removeCategoryChild(self $categoryChild): static
    {
        if ($this->categoryChild->removeElement($categoryChild)) {
            // set the owning side to null (unless already changed)
            if ($categoryChild->getParentCategory() === $this) {
                $categoryChild->setParentCategory(null);
            }
        }

        return $this;
    }
}
