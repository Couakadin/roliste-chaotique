<?php

namespace App\Entity\Table;

use App\Entity\Editor\Editor;
use App\Entity\Event\Event;
use App\Entity\Event\EventColor;
use App\Entity\Genre\Genre;
use App\Entity\System\System;
use App\Entity\User\User;
use App\Repository\Table\TableRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TableRepository::class)]
#[ORM\Table(name: 'rc_table')]
#[UniqueEntity(fields: ['slug'], message: 'entity.unique')]
class Table
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255, maxMessage: 'entity.length.max')]
    #[Assert\NotBlank(message: 'entity.not_blank')]
    private ?string $name = null;

    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ['name'])]
    #[Assert\Length(max: 128, maxMessage: 'entity.length.max')]
    private ?string $slug = null;

    #[ORM\Column]
    private ?bool $showcase = null;

    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Length(max: 180, maxMessage: 'entity.length.max')]
    #[Assert\File(
        maxSize: '3000k',
        mimeTypes: [
            'image/jpeg',
            'image/png',
            'image/svg+xml'
        ],
        maxSizeMessage: 'entity.file.size',
        mimeTypesMessage: 'entity.file.type'
    )]
    private ?string $picture = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'table', targetEntity: Event::class, orphanRemoval: true)]
    private Collection $events;

    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: 'tables')]
    #[ORM\JoinTable(name: 'rc_table_genre')]
    private Collection $genre;

    #[ORM\ManyToOne(inversedBy: 'tables')]
    private ?Editor $editor = null;

    #[ORM\ManyToOne(inversedBy: 'tables')]
    private ?System $system = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'tables')]
    #[ORM\JoinTable(name: 'rc_table_favorite')]
    private Collection $favorite;

    #[ORM\OneToMany(mappedBy: 'table', targetEntity: EventColor::class)]
    private Collection|ArrayCollection $eventColors;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->genre = new ArrayCollection();
        $this->favorite = new ArrayCollection();
        $this->eventColors = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName() ?? 'n/a';
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
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isShowcase(): ?bool
    {
        return $this->showcase;
    }

    /**
     * @param bool $showcase
     *
     * @return $this
     */
    public function setShowcase(bool $showcase): self
    {
        $this->showcase = $showcase;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    /**
     * @param string|null $picture
     *
     * @return $this
     */
    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     *
     * @return $this
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeImmutable $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeImmutable $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    /**
     * @param Event $event
     *
     * @return $this
     */
    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setTable($this);
        }

        return $this;
    }

    /**
     * @param Event $event
     *
     * @return $this
     */
    public function removeEvent(Event $event): self
    {
        // set the owning side to null (unless already changed)
        if ($this->events->removeElement($event) && $event->getTable() === $this) {
            $event->setTable(null);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getGenre(): Collection
    {
        return $this->genre;
    }

    /**
     * @param Genre $genre
     *
     * @return $this
     */
    public function addGenre(Genre $genre): self
    {
        if (!$this->genre->contains($genre)) {
            $this->genre->add($genre);
        }

        return $this;
    }

    /**
     * @param Genre $genre
     *
     * @return $this
     */
    public function removeGenre(Genre $genre): self
    {
        $this->genre->removeElement($genre);

        return $this;
    }

    /**
     * @return Editor|null
     */
    public function getEditor(): ?Editor
    {
        return $this->editor;
    }

    /**
     * @param Editor|null $editor
     *
     * @return $this
     */
    public function setEditor(?Editor $editor): self
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * @return System|null
     */
    public function getSystem(): ?System
    {
        return $this->system;
    }

    /**
     * @param System|null $system
     *
     * @return $this
     */
    public function setSystem(?System $system): self
    {
        $this->system = $system;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getFavorite(): Collection
    {
        return $this->favorite;
    }

    /**
     * @param User|UserInterface $favorite
     *
     * @return $this
     */
    public function addFavorite(User|UserInterface $favorite): self
    {
        if (!$this->favorite->contains($favorite)) {
            $this->favorite->add($favorite);
        }

        return $this;
    }

    /**
     * @param User|UserInterface $favorite
     *
     * @return $this
     */
    public function removeFavorite(User|UserInterface $favorite): self
    {
        $this->favorite->removeElement($favorite);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getEventColors(): Collection
    {
        return $this->eventColors;
    }

    /**
     * @param EventColor $eventColor
     *
     * @return $this
     */
    public function addEventColor(EventColor $eventColor): self
    {
        if (!$this->eventColors->contains($eventColor)) {
            $this->eventColors->add($eventColor);
            $eventColor->setTable($this);
        }

        return $this;
    }

    /**
     * @param EventColor $eventColor
     *
     * @return $this
     */
    public function removeEventColor(EventColor $eventColor): self
    {
        // set the owning side to null (unless already changed)
        if ($this->eventColors->removeElement($eventColor) && $eventColor->getTable() === $this) {
            $eventColor->setTable(null);
        }

        return $this;
    }
}
