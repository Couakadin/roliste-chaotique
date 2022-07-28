<?php

namespace App\Entity\User;

use App\Entity\Guild\Guild;
use App\Entity\Token\Token;
use App\Entity\Avatar\Avatar;
use App\Repository\User\UserRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="rc_user")
 * @UniqueEntity(
 *     message="user.email.unique",
 *     fields={"email"}
 * )
 * @UniqueEntity(
 *     message="user.username.unique",
 *     fields={"username"}
 * )
 * @UniqueEntity(
 *     message="entity.slug.unique",
 *     fields={"slug"}
 * )
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLES = [
        'ROLE_ADMIN'  => 'ROLE_ADMIN',
        'ROLE_USER'   => 'ROLE_USER',
        'ROLE_EDITOR' => 'ROLE_EDITOR'
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $username;

    /**
     * @Gedmo\Slug(fields={"username"})
     * @ORM\Column(length=128, unique=true)
     */
    private string $slug;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isVerified = false;

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
     * @ORM\Column(type="datetime", nullable=true, options={"default": "CURRENT_TIMESTAMP"})
     */
    private ?DateTimeInterface $loginAt = null;

    /**
     * @ORM\OneToMany(targetEntity=Token::class, mappedBy="user")
     */
    private Collection $tokens;

    /**
     * @ORM\ManyToOne(targetEntity=Avatar::class, inversedBy="user")
     */
    private ?Avatar $avatar = null;

    /**
     * @ORM\OneToMany(targetEntity=Guild::class, mappedBy="master")
     */
    private Collection $guildMaster;

    /**
     * @ORM\ManyToMany(targetEntity=Guild::class, mappedBy="members")
     */
    private Collection $guildMembers;

    public function __construct()
    {
        $this->guildMaster = new ArrayCollection();
        $this->guildMembers = new ArrayCollection();
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

    public function getLoginAt(): ?DateTimeInterface
    {
        return $this->loginAt;
    }

    public function setLoginAt(?DateTimeInterface $loginAt): self
    {
        $this->loginAt = $loginAt;

        return $this;
    }

    /**
     * If loginAt is smaller than the current date from 20min, we suppose the user is logout.
     *
     * @return bool
     */
    public function isLoginAt(): bool
    {
        $now = new DateTime('now -20min');

        if ($this->loginAt->format('Y-m-d H:i:s') < $now->format('Y-m-d H:i:s')) {
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

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
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
     * @return Collection
     */
    public function getGuildMaster(): Collection
    {
        return $this->guildMaster;
    }

    public function addGuildMaster(Guild $guild): self
    {
        if (!$this->guildMaster->contains($guild)) {
            $this->guildMaster[] = $guild;
            $guild->setMaster($this);
        }

        return $this;
    }

    public function removeGuildMaster(Guild $guild): self
    {
        if ($this->guildMaster->removeElement($guild)) {
            // set the owning side to null (unless already changed)
            if ($guild->getMaster() === $this) {
                $guild->setMaster(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getGuildMembers(): Collection
    {
        return $this->guildMembers;
    }

    public function addGuildMember(Guild $guildMember): self
    {
        if (!$this->guildMembers->contains($guildMember)) {
            $this->guildMembers[] = $guildMember;
            $guildMember->addMember($this);
        }

        return $this;
    }

    public function removeGuildMember(Guild $guildMember): self
    {
        if ($this->guildMembers->removeElement($guildMember)) {
            $guildMember->removeMember($this);
        }

        return $this;
    }
}
