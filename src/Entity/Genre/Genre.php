<?php

namespace App\Entity\Genre;

use App\Entity\Table\Table;
use App\Repository\Genre\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
#[ORM\Table(name: 'rc_genre')]
class Genre
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;
    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Table::class, mappedBy: 'genre')]
    private Collection|ArrayCollection $tables;

    public function __construct()
    {
        $this->tables = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function __toString()
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
