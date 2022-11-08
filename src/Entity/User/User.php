<?php

namespace App\Entity\User;

use App\Entity\Avatar\Avatar;
use App\Entity\Badge\BadgeUnlock;
use App\Entity\Event\Event;
use App\Entity\Notification\Notification;
use App\Entity\Storage\Storage;
use App\Entity\Table\Table;
use App\Entity\Token\Token;
use App\Repository\User\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    /**
     * @var string
     */
    #[ORM\Column(length: 180, unique: true)]
    private string $email;
    /**
     * @var string
     */
    #[ORM\Column(length: 180, unique: true)]
    private string $username;
    /**
     * @var string
     */
    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ['username'])]
    private string $slug;
    /**
     * @var array
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];
    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 4096)]
    private string $password;
    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;
    /**
     * @var DateTimeImmutable
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTimeImmutable $createdAt;
    /**
     * @var DateTimeImmutable
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    private DateTimeImmutable $updatedAt;
    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?DateTimeImmutable $loggedAt = null;
    /**
     * @var Collection
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Token::class, orphanRemoval: true)]
    private Collection $tokens;
    /**
     * @var Avatar|null
     */
    #[ORM\ManyToOne(targetEntity: Avatar::class, inversedBy: 'user')]
    private ?Avatar $avatar = null;
    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'master', targetEntity: Event::class, orphanRemoval: true)]
    private Collection|ArrayCollection $eventMaster;
    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'participate')]
    private Collection|ArrayCollection $eventParticipate;
    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BadgeUnlock::class, orphanRemoval: true)]
    private Collection|ArrayCollection $badgeUnlocks;
    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Table::class, mappedBy: 'favorite')]
    private Collection|ArrayCollection $tables;
    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notification::class)]
    private Collection|ArrayCollection $notifications;
    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Storage::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection|ArrayCollection $storages;

    public function __construct()
    {
        $this->eventMaster = new ArrayCollection();
        $this->eventParticipate = new ArrayCollection();
        $this->badgeUnlocks = new ArrayCollection();
        $this->tables = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->storages = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->username;
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
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
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

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
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

    /**
     * @param array $roles
     *
     * @return $this
     */
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

    /**
     * @param string $password
     *
     * @return $this
     */
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

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * @param bool $isVerified
     *
     * @return $this
     */
    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getLoggedAt(): ?DateTimeImmutable
    {
        return $this->loggedAt;
    }

    /**
     * @param DateTimeImmutable|null $loggedAt
     *
     * @return $this
     */
    public function setLoggedAt(?DateTimeImmutable $loggedAt): self
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
        $now = new DateTimeImmutable('now -20min');

        if ($this->loggedAt->format('Y-m-d H:i:s') < $now->format('Y-m-d H:i:s')) {
            return false;
        }
        return true;
    }

    /**
     * @return Collection
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    /**
     * @param Token $token
     *
     * @return $this
     */
    public function addToken(Token $token): self
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens[] = $token;
        }

        return $this;
    }

    /**
     * @param Token $token
     *
     * @return $this
     */
    public function removeToken(Token $token): self
    {
        if ($this->tokens->contains($token)) {
            $this->tokens->removeElement($token);
        }

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
     * @return void
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
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
     * @return void
     */
    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Avatar|null
     */
    public function getAvatar(): ?Avatar
    {
        return $this->avatar;
    }

    /**
     * @param Avatar|null $avatar
     *
     * @return $this
     */
    public function setAvatar(?Avatar $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getEventMaster(): Collection
    {
        return $this->eventMaster;
    }

    /**
     * @param Event $eventMaster
     *
     * @return $this
     */
    public function addEventMaster(Event $eventMaster): self
    {
        if (!$this->eventMaster->contains($eventMaster)) {
            $this->eventMaster->add($eventMaster);
            $eventMaster->setMaster($this);
        }

        return $this;
    }

    /**
     * @param Event $eventMaster
     *
     * @return $this
     */
    public function removeEventMaster(Event $eventMaster): self
    {
        // set the owning side to null (unless already changed)
        if ($this->eventMaster->removeElement($eventMaster) && $eventMaster->getMaster() === $this) {
            $eventMaster->setMaster(null);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getEventParticipates(): Collection
    {
        return $this->eventParticipate;
    }

    /**
     * @param Event $eventParticipate
     *
     * @return $this
     */
    public function addEventParticipate(Event $eventParticipate): self
    {
        if (!$this->eventParticipate->contains($eventParticipate)) {
            $this->eventParticipate->add($eventParticipate);
            $eventParticipate->addParticipate($this);
        }

        return $this;
    }

    /**
     * @param Event $eventParticipate
     *
     * @return $this
     */
    public function removeEventParticipate(Event $eventParticipate): self
    {
        if ($this->eventParticipate->removeElement($eventParticipate)) {
            $eventParticipate->removeParticipate($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getBadgeUnlocks(): Collection
    {
        return $this->badgeUnlocks;
    }

    /**
     * @param BadgeUnlock $badgeUnlock
     *
     * @return $this
     */
    public function addBadgeUnlock(BadgeUnlock $badgeUnlock): self
    {
        if (!$this->badgeUnlocks->contains($badgeUnlock)) {
            $this->badgeUnlocks->add($badgeUnlock);
            $badgeUnlock->setUser($this);
        }

        return $this;
    }

    /**
     * @param BadgeUnlock $badgeUnlock
     *
     * @return $this
     */
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

    /**
     * @return Collection
     */
    public function getTables(): Collection
    {
        return $this->tables;
    }

    /**
     * @param Table $table
     *
     * @return $this
     */
    public function addTable(Table $table): self
    {
        if (!$this->tables->contains($table)) {
            $this->tables->add($table);
            $table->addFavorite($this);
        }

        return $this;
    }

    /**
     * @param Table $table
     *
     * @return $this
     */
    public function removeTable(Table $table): self
    {
        if ($this->tables->removeElement($table)) {
            $table->removeFavorite($this);
        }

        return $this;
    }

    /**
     * @return Collection
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
            $notification->setUser($this);
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
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getStorages(): Collection
    {
        return $this->storages;
    }

    /**
     * @param Storage $storage
     *
     * @return $this
     */
    public function addStorage(Storage $storage): self
    {
        if (!$this->storages->contains($storage)) {
            $this->storages->add($storage);
            $storage->setUser($this);
        }

        return $this;
    }

    /**
     * @param Storage $storage
     *
     * @return $this
     */
    public function removeStorage(Storage $storage): self
    {
        if ($this->storages->removeElement($storage)) {
            // set the owning side to null (unless already changed)
            if ($storage->getUser() === $this) {
                $storage->setUser(null);
            }
        }

        return $this;
    }
}
