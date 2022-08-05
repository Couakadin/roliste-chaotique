<?php

namespace App\Entity\Table;

use App\Entity\User\User;
use App\Repository\Table\TableMemberRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TableMemberRepository::class)]
#[ORM\Table(name: 'rc_table_member')]
class TableMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tableMembers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'tableMembers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Table $table = null;

    #[ORM\Column]
    private ?bool $validate = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTable(): ?Table
    {
        return $this->table;
    }

    public function setTable(?Table $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function isValidate(): ?bool
    {
        return $this->validate;
    }

    public function setValidate(bool $validate): self
    {
        $this->validate = $validate;

        return $this;
    }
}
