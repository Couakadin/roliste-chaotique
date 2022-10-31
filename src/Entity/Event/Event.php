<?php

namespace App\Entity\Event;

use App\Entity\Table\Table;
use App\Entity\User\User;
use App\Entity\Zone\Zone;
use App\Repository\Event\EventRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table(name: 'rc_event')]
class Event
{
    public const TYPE = [
        'campaign' => 'campaign',
        'one-shot' => 'one-shot'
    ];

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
     * @var string|null
     */
    #[ORM\Column(length: 128)]
    #[Gedmo\Slug(fields: ['name'])]
    private ?string $slug = null;
    /**
     * @var string|null
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;
    /**
     * @var string|null
     */
    #[ORM\Column(length: 15)]
    private ?string $type = null;
    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    private ?int $totalParticipate = null;
    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $start = null;
    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $end = null;
    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?DateTimeImmutable $createdAt = null;
    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?DateTimeImmutable $updatedAt = null;
    /**
     * @var User|null
     */
    #[ORM\ManyToOne(inversedBy: 'eventMaster')]
    private ?User $master = null;
    /**
     * @var Table|null
     */
    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Table $table = null;
    /**
     * @var Zone|null
     */
    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?Zone $zone = null;
    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'eventParticipate', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'rc_event_participate')]
    private Collection|ArrayCollection $participate;

    public function __construct()
    {
        $this->participate = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
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
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTotalParticipate(): ?int
    {
        return $this->totalParticipate;
    }

    /**
     * @param int|null $totalParticipate
     *
     * @return $this
     */
    public function setTotalParticipate(?int $totalParticipate): self
    {
        $this->totalParticipate = $totalParticipate;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getStart(): ?DateTimeImmutable
    {
        return $this->start;
    }

    /**
     * @param DateTimeImmutable $start
     *
     * @return $this
     */
    public function setStart(DateTimeImmutable $start): self
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getEnd(): ?DateTimeImmutable
    {
        return $this->end;
    }

    /**
     * @param DateTimeImmutable|null $end
     *
     * @return $this
     */
    public function setEnd(?DateTimeImmutable $end): self
    {
        $this->end = $end;

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
     * @return User|null
     */
    public function getMaster(): ?User
    {
        return $this->master;
    }

    /**
     * @param User|null $master
     *
     * @return $this
     */
    public function setMaster(?User $master): self
    {
        $this->master = $master;

        return $this;
    }

    /**
     * @return Table|null
     */
    public function getTable(): ?Table
    {
        return $this->table;
    }

    /**
     * @param Table|null $table
     *
     * @return $this
     */
    public function setTable(?Table $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return Zone|null
     */
    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    /**
     * @param Zone|null $zone
     *
     * @return $this
     */
    public function setZone(?Zone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getParticipate(): Collection
    {
        return $this->participate;
    }

    /**
     * @param User|UserInterface $participate
     *
     * @return $this
     */
    public function addParticipate(User|UserInterface $participate): self
    {
        if (!$this->participate->contains($participate)) {
            $this->participate->add($participate);
        }

        return $this;
    }

    /**
     * @param User|UserInterface $participate
     *
     * @return $this
     */
    public function removeParticipate(User|UserInterface $participate): self
    {
        $this->participate->removeElement($participate);

        return $this;
    }
}
