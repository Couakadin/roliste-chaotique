<?php

namespace App\Entity\User;

use App\Repository\User\UserParameterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserParameterRepository::class)]
#[ORM\Table(name: 'rc_user_parameter')]
class UserParameter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'userParameter', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    private ?bool $eventEmailReminder = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isEventEmailReminder(): ?bool
    {
        return $this->eventEmailReminder;
    }

    public function setEventEmailReminder(bool $eventEmailReminder): self
    {
        $this->eventEmailReminder = $eventEmailReminder;

        return $this;
    }
}
