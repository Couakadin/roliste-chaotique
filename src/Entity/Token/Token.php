<?php

namespace App\Entity\Token;

use App\Entity\User\User;
use App\Repository\Token\TokenRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
#[ORM\Table(name: 'rc_token')]
class Token
{
    const EMAIL_VERIFY = 'email_verify';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 52, unique: true)]
    private string $token;

    #[ORM\Column(length: 25)]
    private string $type;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private DateTime $expiredAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tokens')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    /**
     * @param User   $user
     * @param string $type
     *
     * @throws Exception
     */
    public function __construct(User $user, string $type)
    {
        $this->token     = bin2hex(random_bytes(21));
        $this->user      = $user;
        $this->type      = $type;
        $this->expiredAt = new DateTime('+1 hour');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getExpiredAt(): ?DateTimeInterface
    {
        return $this->expiredAt;
    }

    public function renewExpiredAt()
    {
        $this->expiredAt = new DateTime('+1 hour');
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}

