<?php

namespace App\Entity\Token;

use App\Entity\User\User;
use App\Repository\Token\TokenRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
#[ORM\Table(name: 'rc_token')]
class Token
{
    public const EMAIL_VERIFY = 'email_verify';
    public const FORGOTTEN_PASSWORD = 'forgotten_password';
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    /**
     * @var string|null
     */
    #[ORM\Column(length: 52, unique: true, nullable: true)]
    private ?string $token;
    /**
     * @var string
     */
    #[ORM\Column(length: 25)]
    private string $type;
    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $expiredAt;
    /**
     * @var User
     */
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'tokens')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    /**
     * @param User $user
     * @param string $type
     *
     * @throws Exception
     */
    public function __construct(User $user, string $type)
    {
        $this->token = bin2hex(random_bytes(21));
        $this->user = $user;
        $this->type = $type;
        $this->expiredAt = new DateTimeImmutable('+1 hour');
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
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getExpiredAt(): ?DateTimeInterface
    {
        return $this->expiredAt;
    }

    /**
     * @return void
     */
    public function renewExpiredAt(): void
    {
        $this->expiredAt = new DateTimeImmutable('+1 hour');
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public function renewToken(): string
    {
        return $this->token = bin2hex(random_bytes(21));
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}

