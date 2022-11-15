<?php

namespace App\Entity\User;

use App\Entity\Avatar\Avatar;
use App\Entity\Badge\BadgeUnlock;
use App\Entity\Event\Event;
use App\Entity\Folder\Folder;
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
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'rc_user')]
#[UniqueEntity(fields: ['email'], message: 'entity.unique')]
#[UniqueEntity(fields: ['username'], message: 'entity.unique')]
#[UniqueEntity(fields: ['slug'], message: 'entity.unique')]
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
    #[Assert\NotBlank(message: 'entity.not_blank')]
    #[Assert\NotNull(message: 'entity.not_blank')]
    #[Assert\Email(message: 'entity.email')]
    #[Assert\Length(max: 180, maxMessage: 'entity.length.max')]
    private string $email;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'entity.not_blank')]
    #[Assert\NotNull(message: 'entity.not_blank')]
    #[Assert\Length(min: 3, max: 180, minMessage: 'entity.length.min', maxMessage: 'entity.length.max')]
    private string $username;

    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ['username'])]
    #[Assert\Length(max: 128, maxMessage: 'entity.length.max')]
    private ?string $slug = null;

    #[ORM\Column(type: 'json')]
    #[Assert\Unique(message: 'entity.unique')]
    #[Assert\Type(type: 'array', message: 'entity.type')]
    private ?array $roles = [];
    /**
     * @var string|null The hashed password
     */
    #[ORM\Column(length: 4096)]
    #[Assert\Length(min: 6, minMessage: 'entity.length.min')]
    #[Assert\Length(min: 6, max: 4096, minMessage: 'entity.length.min', maxMessage: 'entity.length.max')]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    #[Assert\NotNull(message: 'entity.not_blank')]
    private ?bool $isVerified = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?DateTimeImmutable $loggedAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Token::class, orphanRemoval: true)]
    private ?Collection $tokens;

    #[ORM\ManyToOne(targetEntity: Avatar::class, inversedBy: 'user')]
    private ?Avatar $avatar = null;

    #[ORM\OneToMany(mappedBy: 'master', targetEntity: Event::class, orphanRemoval: true)]
    private Collection|ArrayCollection $eventMaster;

    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'participate')]
    private Collection|ArrayCollection $eventParticipate;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BadgeUnlock::class, orphanRemoval: true)]
    private Collection|ArrayCollection $badgeUnlocks;

    #[ORM\ManyToMany(targetEntity: Table::class, mappedBy: 'favorite')]
    private Collection|ArrayCollection $tables;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notification::class)]
    private Collection|ArrayCollection $notifications;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Storage::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection|ArrayCollection $storages;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Folder::class, orphanRemoval: true)]
    private ?Collection $folders;

    public function __construct()
    {
        $this->eventMaster = new ArrayCollection();
        $this->eventParticipate = new ArrayCollection();
        $this->badgeUnlocks = new ArrayCollection();
        $this->tables = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->storages = new ArrayCollection();
        $this->folders = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
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
    public function eraseCredentials(): void
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

        return $this->loggedAt->format('Y-m-d H:i:s') >= $now->format('Y-m-d H:i:s');
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
     * @return DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
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
        // set the owning side to null (unless already changed)
        if ($this->badgeUnlocks->removeElement($badgeUnlock) && $badgeUnlock->getUser() === $this) {
            $badgeUnlock->setUser(null);
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
        // set the owning side to null (unless already changed)
        if ($this->notifications->removeElement($notification) && $notification->getUser() === $this) {
            $notification->setUser(null);
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
        // set the owning side to null (unless already changed)
        if ($this->storages->removeElement($storage) && $storage->getUser() === $this) {
            $storage->setUser(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Folder>
     */
    public function getFolders(): Collection
    {
        return $this->folders;
    }

    public function addFolder(Folder $folder): self
    {
        if (!$this->folders->contains($folder)) {
            $this->folders->add($folder);
            $folder->setOwner($this);
        }

        return $this;
    }

    public function removeFolder(Folder $folder): self
    {
        // set the owning side to null (unless already changed)
        if ($this->folders->removeElement($folder) && $folder->getOwner() === $this) {
            $folder->setOwner(null);
        }

        return $this;
    }
}
