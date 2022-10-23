<?php

namespace App\Entity\User;

use App\Entity\Avatar\Avatar;
use App\Entity\Event\Event;
use App\Entity\Table\Table;
use App\Entity\Token\Token;
use App\RC\BadgeBundle\src\Entity\BadgeUnlock;
use App\Repository\User\UserRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'rc_user')]
#[UniqueEntity(fields: ['email', 'username', 'slug'], message: 'entity.unique')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLES = [
        'ROLE_ADMIN'  => 'ROLE_ADMIN',
        'ROLE_USER'   => 'ROLE_USER',
        'ROLE_EDITOR' => 'ROLE_EDITOR'
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    #[ORM\Column(length: 180, unique: true)]
    private string $username;

    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ['username'])]
    private string $slug;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 4096)]
    private string $password;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'update')]
    private DateTime $updatedAt;

    #[ORM\Column(type: 'datetime', nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?DateTimeInterface $loggedAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Token::class, orphanRemoval: true)]
    private Collection $tokens;

    #[ORM\ManyToOne(targetEntity: Avatar::class, inversedBy: 'user')]
    private ?Avatar $avatar = null;

    #[ORM\OneToMany(mappedBy: 'master', targetEntity: Event::class)]
    private Collection $eventMaster;

    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'participate')]
    private Collection $eventParticipate;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BadgeUnlock::class, orphanRemoval: true)]
    private Collection $badgeUnlocks;

    public function __construct()
    {
        $this->eventMaster = new ArrayCollection();
        $this->eventParticipate = new ArrayCollection();
        $this->badgeUnlocks = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->username;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Return the slug created from the username.
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

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getLoggedAt(): ?DateTimeInterface
    {
        return $this->loggedAt;
    }

    public function setLoggedAt(?DateTimeInterface $loggedAt): self
    {
        $this->loggedAt = $loggedAt;

        return $this;
    }

    /**
     * If loggedAt is smaller than the current date from 20min, we suppose the user is logout.
     *
     * @return bool
     */
    public function isLoggedAt(): bool
    {
        $now = new DateTime('now -20min');

        if ($this->loggedAt->format('Y-m-d H:i:s') < $now->format('Y-m-d H:i:s')) {
            return false;
        }
        return true;
    }

    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function addToken(Token $token): self
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens[] = $token;
        }

        return $this;
    }

    public function removeToken(Token $token): self
    {
        if ($this->tokens->contains($token)) {
            $this->tokens->removeElement($token);
        }

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

    public function getAvatar(): ?Avatar
    {
        return $this->avatar;
    }

    public function setAvatar(?Avatar $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEventMaster(): Collection
    {
        return $this->eventMaster;
    }

    public function addEventMaster(Event $eventMaster): self
    {
        if (!$this->eventMaster->contains($eventMaster)) {
            $this->eventMaster->add($eventMaster);
            $eventMaster->setMaster($this);
        }

        return $this;
    }

    public function removeEventMaster(Event $eventMaster): self
    {
        // set the owning side to null (unless already changed)
        if ($this->eventMaster->removeElement($eventMaster) && $eventMaster->getMaster() === $this) {
            $eventMaster->setMaster(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Table>
     */
    public function getEventParticipates(): Collection
    {
        return $this->eventParticipate;
    }

    public function addEventParticipate(Event $eventParticipate): self
    {
        if (!$this->eventParticipate->contains($eventParticipate)) {
            $this->eventParticipate->add($eventParticipate);
            $eventParticipate->addParticipate($this);
        }

        return $this;
    }

    public function removeEventParticipate(Event $eventParticipate): self
    {
        if ($this->eventParticipate->removeElement($eventParticipate)) {
            $eventParticipate->removeParticipate($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, BadgeUnlock>
     */
    public function getBadgeUnlocks(): Collection
    {
        return $this->badgeUnlocks;
    }

    public function addBadgeUnlock(BadgeUnlock $badgeUnlock): self
    {
        if (!$this->badgeUnlocks->contains($badgeUnlock)) {
            $this->badgeUnlocks->add($badgeUnlock);
            $badgeUnlock->setUser($this);
        }

        return $this;
    }

    public function removeBadgeUnlock(BadgeUnlock $badgeUnlock): self
    {
        if ($this->badgeUnlocks->removeElement($badgeUnlock)) {
            // set the owning side to null (unless already changed)
            if ($badgeUnlock->getUser() === $this) {
                $badgeUnlock->setUser(null);
            }
        }

        return $this;
    }
}
