<?php

namespace App\Entity\Badge;

use App\Entity\User\User;
use App\Repository\Badge\BadgeUnlockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BadgeUnlockRepository::class)]
#[ORM\Table(name: 'rc_badge_unlock')]
class BadgeUnlock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'unlocks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Badge $badge = null;

    #[ORM\ManyToOne(inversedBy: 'badgeUnlocks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    public function setBadge(?Badge $badge): self
    {
        $this->badge = $badge;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
