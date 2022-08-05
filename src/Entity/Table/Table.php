<?php

namespace App\Entity\Table;

use App\Entity\Event\Event;
use App\Entity\User\User;
use App\Repository\Table\TableRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: TableRepository::class)]
#[ORM\Table(name: 'rc_table')]
class Table
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 128)]
    #[Gedmo\Slug(fields: ['name'])]
    private ?string $slug = null;

    #[ORM\Column]
    private ?bool $showcase = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $picture = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'table', targetEntity: Event::class, orphanRemoval: true)]
    private Collection $events;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'tables')]
    #[ORM\JoinTable(name: 'rc_table_member')]
    private Collection $members;

    #[ORM\OneToMany(mappedBy: 'table', targetEntity: TableInscription::class, orphanRemoval: true)]
    private Collection $tableInscriptions;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->members = new ArrayCollection();
        $this->tableInscriptions = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName() ?? 'n/a';
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function isShowcase(): ?bool
    {
        return $this->showcase;
    }

    public function setShowcase(bool $showcase): self
    {
        $this->showcase = $showcase;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setTable($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        // set the owning side to null (unless already changed)
        if ($this->events->removeElement($event) && $event->getTable() === $this) {
            $event->setTable(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        $this->members->removeElement($member);

        return $this;
    }

    /**
     * @return Collection<int, TableInscription>
     */
    public function getTableInscriptions(): Collection
    {
        return $this->tableInscriptions;
    }

    public function addTableInscription(TableInscription $tableInscription): self
    {
        if (!$this->tableInscriptions->contains($tableInscription)) {
            $this->tableInscriptions->add($tableInscription);
            $tableInscription->setTables($this);
        }

        return $this;
    }

    public function removeTableInscription(TableInscription $tableInscription): self
    {
        if ($this->tableInscriptions->removeElement($tableInscription)) {
            // set the owning side to null (unless already changed)
            if ($tableInscription->getTables() === $this) {
                $tableInscription->setTables(null);
            }
        }

        return $this;
    }
}
