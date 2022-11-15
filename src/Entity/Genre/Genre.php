<?php

namespace App\Entity\Genre;

use App\Entity\Table\Table;
use App\Repository\Genre\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
#[ORM\Table(name: 'rc_genre')]
class Genre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255, maxMessage: 'entity.length.max')]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Table::class, mappedBy: 'genre')]
    private Collection|ArrayCollection $tables;

    public function __construct()
    {
        $this->tables = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTables(): Collection
    {
        return $this->tables;
    }

    /**
     * @param Table $table
     *
     * @return $this
     */
    public function addTable(Table $table): self
    {
        if (!$this->tables->contains($table)) {
            $this->tables->add($table);
            $table->addGenre($this);
        }

        return $this;
    }

    /**
     * @param Table $table
     *
     * @return $this
     */
    public function removeTable(Table $table): self
    {
        if ($this->tables->removeElement($table)) {
            $table->removeGenre($this);
        }

        return $this;
    }
}
