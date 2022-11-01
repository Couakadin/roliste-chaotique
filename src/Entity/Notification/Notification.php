<?php

namespace App\Entity\Notification;

use App\Entity\Badge\Badge;
use App\Entity\Event\Event;
use App\Entity\User\User;
use App\Repository\Notification\NotificationRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\Table(name: 'rc_notification')]
class Notification
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
    private ?string $type = null;
    /**
     * @var bool|null
     */
    #[ORM\Column(options: ['default' => false])]
    private ?bool $isRead = null;
    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'create')]
    private ?DateTimeImmutable $createdAt = null;
    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'update')]
    private ?DateTimeImmutable $updatedAt = null;
    /**
     * @var User|null
     */
    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'notifications')]
    private ?User $user = null;
    /**
     * @var Event|null
     */
    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'notifications')]
    private ?Event $event = null;
    /**
     * @var Badge|null
     */
    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'notifications')]
    private ?Badge $badge = null;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    private ?User $participate = null;

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
     * @return bool|null
     */
    public function isRead(): ?bool
    {
        return $this->isRead;
    }

    /**
     * @param bool $isRead
     *
     * @return $this
     */
    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

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
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     *
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Event|null
     */
    public function getEvent(): ?Event
    {
        return $this->event;
    }

    /**
     * @param Event|null $event
     *
     * @return $this
     */
    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return Badge|null
     */
    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    /**
     * @param Badge|null $badge
     *
     * @return $this
     */
    public function setBadge(?Badge $badge): self
    {
        $this->badge = $badge;

        return $this;
    }

    public function getParticipate(): ?User
    {
        return $this->participate;
    }

    public function setParticipate(?User $participate): self
    {
        $this->participate = $participate;

        return $this;
    }
}
