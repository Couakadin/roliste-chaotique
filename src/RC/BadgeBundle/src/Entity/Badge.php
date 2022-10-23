<?php

namespace App\RC\BadgeBundle\src\Entity;

use App\RC\BadgeBundle\src\Repository\BadgeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BadgeRepository::class)]
#[ORM\Table(name: 'rc_badge')]
class Badge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $actionName = null;

    #[ORM\Column]
    private ?int $actionCount = null;

    #[ORM\OneToMany(mappedBy: 'badge', targetEntity: BadgeUnlock::class, orphanRemoval: true)]
    private Collection $unlocks;

    public function __construct()
    {
        $this->unlocks = new ArrayCollection();
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

    public function getActionName(): ?string
    {
        return $this->actionName;
    }

    public function setActionName(string $actionName): self
    {
        $this->actionName = $actionName;

        return $this;
    }

    public function getActionCount(): ?int
    {
        return $this->actionCount;
    }

    public function setActionCount(int $actionCount): self
    {
        $this->actionCount = $actionCount;

        return $this;
    }

    /**
     * @return Collection<int, BadgeUnlock>
     */
    public function getUnlocks(): Collection
    {
        return $this->unlocks;
    }

    public function addUnlock(BadgeUnlock $unlock): self
    {
        if (!$this->unlocks->contains($unlock)) {
            $this->unlocks->add($unlock);
            $unlock->setBadge($this);
        }

        return $this;
    }

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
}
