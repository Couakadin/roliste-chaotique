<?php

namespace App\Entity\Badge;

use App\Entity\Notification\Notification;
use App\Repository\Badge\BadgeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: BadgeRepository::class)]
#[ORM\Table(name: 'rc_badge')]
class Badge
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    /**
     * @var int
     */
    #[Gedmo\SortablePosition]
    #[ORM\Column(name: 'position', type: 'integer')]
    private int $position;
    /**
     * @var string|null
     */
    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;
    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $description = null;
    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $actionName = null;
    /**
     * @var int|null
     */
    #[ORM\Column]
    private ?int $actionCount = null;
    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'badge', targetEntity: BadgeUnlock::class, orphanRemoval: true)]
    private Collection|ArrayCollection $unlocks;
    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'badge', targetEntity: Notification::class)]
    private Collection|ArrayCollection $notifications;

    public function __construct()
    {
        $this->unlocks = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $position
     *
     * @return void
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
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
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getActionName(): ?string
    {
        return $this->actionName;
    }

    /**
     * @param string $actionName
     *
     * @return $this
     */
    public function setActionName(string $actionName): self
    {
        $this->actionName = $actionName;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getActionCount(): ?int
    {
        return $this->actionCount;
    }

    /**
     * @param int $actionCount
     *
     * @return $this
     */
    public function setActionCount(int $actionCount): self
    {
        $this->actionCount = $actionCount;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getUnlocks(): Collection
    {
        return $this->unlocks;
    }

    /**
     * @param BadgeUnlock $unlock
     *
     * @return $this
     */
    public function addUnlock(BadgeUnlock $unlock): self
    {
        if (!$this->unlocks->contains($unlock)) {
            $this->unlocks->add($unlock);
            $unlock->setBadge($this);
        }

        return $this;
    }

    /**
     * @param BadgeUnlock $unlock
     *
     * @return $this
     */
    public function removeUnlock(BadgeUnlock $unlock): self
    {
        if ($this->unlocks->removeElement($unlock)) {
            // set the owning side to null (unless already changed)
            if ($unlock->getBadge() === $this) {
                $unlock->setBadge(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    /**
     * @param Notification $notification
     *
     * @return $this
     */
    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setBadge($this);
        }

        return $this;
    }

    /**
     * @param Notification $notification
     *
     * @return $this
     */
    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getBadge() === $this) {
                $notification->setBadge(null);
            }
        }

        return $this;
    }
}
