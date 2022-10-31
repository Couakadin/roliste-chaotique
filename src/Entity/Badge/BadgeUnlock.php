<?php

namespace App\Entity\Badge;

use App\Entity\User\User;
use App\Repository\Badge\BadgeUnlockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BadgeUnlockRepository::class)]
#[ORM\Table(name: 'rc_badge_unlock')]
class BadgeUnlock
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Badge|null
     */
    #[ORM\ManyToOne(inversedBy: 'unlocks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Badge $badge = null;

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(inversedBy: 'badgeUnlocks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
}
