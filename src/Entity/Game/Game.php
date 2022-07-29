<?php

namespace App\Entity\Game;

use App\Entity\Guild\Guild;
use App\Repository\Game\GameRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 * @ORM\Table(name="rc_game")
 * @UniqueEntity(
 *     message="entity.name.unique",
 *     fields={"name"}
 * )
 * @UniqueEntity(
 *     message="entity.slug.unique",
 *     fields={"slug"}
 * )
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private string $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $content;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private ?string $picture = null;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private bool $showcase = false;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private DateTime $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=Guild::class, mappedBy="games")
     */
    private Collection $guildGames;

    public function __construct()
    {
        $this->guildGames = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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

    /**
     * Return the slug created from the name.
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
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

    public function isShowcase(): bool
    {
        return $this->showcase;
    }

    public function setShowcase(bool $showcase): self
    {
        $this->showcase = $showcase;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getGuildGames(): Collection
    {
        return $this->guildGames;
    }

    public function addGuildGames(Guild $guildGame): self
    {
        if (!$this->guildGames->contains($guildGame)) {
            $this->guildGames[] = $guildGame;
            $guildGame->addGame($this);
        }

        return $this;
    }

    public function removeGuildGame(Guild $guildGame): self
    {
        if ($this->guildGames->removeElement($guildGame)) {
            $guildGame->removeGame($this);
        }

        return $this;
    }
}
