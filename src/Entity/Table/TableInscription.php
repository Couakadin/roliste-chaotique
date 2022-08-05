<?php

namespace App\Entity\Table;

use App\Entity\User\User;
use App\Repository\Table\TableInscriptionRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: TableInscriptionRepository::class)]
#[ORM\Table(name: 'rc_table_inscription')]
class TableInscription
{
    public const STATUS = [
        'waiting' => 'waiting',
        'accepted' => 'accepted',
        'declined' => 'declined'
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tableInscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Table $table = null;

    #[ORM\ManyToOne(inversedBy: 'tableInscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 15, options: ['default' => self::STATUS['waiting']])]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?DateTimeInterface $CreatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?DateTimeInterface $updatedAt = null;

    private ?bool $isEmailSending = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTable(): ?Table
    {
        return $this->table;
    }

    public function setTable(?Table $table): self
    {
        $this->table = $table;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(DateTimeInterface $CreatedAt): self
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isEmailSending(): bool
    {
        return $this->isEmailSending;
    }

    public function setIsEmailSending(bool $isEmailSending): self
    {
        $this->isEmailSending = $isEmailSending;

        return $this;
    }
}
